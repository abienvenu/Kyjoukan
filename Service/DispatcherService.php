<?php

namespace Abienvenu\KyjoukanBundle\Service;

use Abienvenu\KyjoukanBundle\Entity\Game;
use Abienvenu\KyjoukanBundle\Entity\Phase;
use Abienvenu\KyjoukanBundle\Entity\Pool;
use Abienvenu\KyjoukanBundle\Entity\Round;
use Abienvenu\KyjoukanBundle\Entity\Team;
use Abienvenu\KyjoukanBundle\Enum\Rule;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class DispatcherService
{
	private $em;
	private $ranker;

	public function __construct(EntityManagerInterface $em, RankService $ranker)
	{
		$this->em = $em;
		$this->ranker = $ranker;
	}

	/**
	 * Load teams from Event and copy them into the Phase
	 *
	 * @param Phase $phase
	 * @return int The number of loaded teams
	 */
	public function loadTeamsIntoPhase(Phase $phase)
	{
		$loaded = 0;
		foreach ($phase->getEvent()->getTeams() as $team)
		{
			if (!$phase->hasTeam($team))
			{
				$phase->addTeam($team);
				$loaded++;
			}
		}
		$this->em->flush();
		return $loaded;
	}

	/**
	 * Dispatch teams into pools of the phase
	 * Do not dispatch teams that are already in a pool
	 *
	 * @param Phase $phase
	 * @return int The number of dispatched teams
	 */
	public function dispatchTeamsIntoPools(Phase $phase)
	{
		$dispatched = 0;
		foreach ($phase->getTeams() as $team)
		{
			if (!$phase->isTeamPooled($team))
			{
				$pool = $phase->getSmallestPool();
				if ($pool)
				{
					$pool->addTeam($team);
					$dispatched++;
				}
			}
		}
		$this->em->flush();
		return $dispatched;
	}

	/**
	 * Remove all unplayed games and empty rounds
	 *
	 * @param Phase $phase
	 */
	public function cleanGames(Phase $phase)
	{
		// Delete all games that have not been played
		foreach ($phase->getPools() as $pool)
		{
			/** @var Game $game */
			foreach ($pool->getGames() as $game)
			{
				if (!$game->isPlayed())
				{
					$pool->removeGame($game);
					$game->getRound()->removeGame($game);
					$this->em->remove($game);
				}
			}
		}

		// Delete empty rounds
		foreach ($phase->getRounds() as $round)
		{
			if (!count($round->getGames()))
			{
				$phase->removeRound($round);
				$this->em->remove($round);
			}
		}
		$this->em->flush();
	}

	/**
	 * Dispatch all the games into the phase, for every pool
	 *
	 * @param Phase $phase
	 */
	public function shuffleGames(Phase $phase)
	{
		if ($phase->getRule() == Rule::ROUNDROBIN)
		{
			$this->dispatchRoundRobinGames($phase);
		}
		else if ($phase->getRule() == Rule::BRACKETS)
		{
			$this->dispatchBracketGames($phase);
		}
		else if ($phase->getRule() == Rule::CUMULATIVERANK)
		{
			$this->dispatchCumulativeRankGames($phase);
		}
		// The phase is fully scheduled with playing teams
		// Now let's find referees for each game
		$this->findReferees($phase);

		$this->em->flush();
	}

	/**
	 * Try to set a referee for each game
	 *
	 * @param Phase $phase
	 */
	protected function findReferees(Phase $phase)
	{
		$refereeCounter = [];

		foreach ($phase->getRounds() as $round)
		{
			foreach ($round->getGames() as $game)
			{
				if ($game->getReferee())
				{
					// The game already has a referee
					continue;
				}

				$poolTeams = $game->getPool()->getTeams();
				$teams = $phase->getTeams()->toArray();
				usort($teams,
				      function (Team $a, Team $b) use ($refereeCounter, $poolTeams)
				      {
					      // Priority for teams in the same pool, because they know each other
					      if ($poolTeams->contains($a) && !$poolTeams->contains($b))
					      {
						      return -1;
					      }
					      if (!$poolTeams->contains($a) && $poolTeams->contains($b))
					      {
						      return 1;
					      }
					      // Now priority to the teams that made few referees
					      if (!isset($refereeCounter[$a->getId()]))
					      {
						      return -1;
					      }
					      if (!isset($refereeCounter[$b->getId()]))
					      {
						      return 1;
					      }
					      return $refereeCounter[$a->getId()] > $refereeCounter[$b->getId()];
				      }
				);

				// Go find a referee
				/** @var Team $team */
				foreach ($teams as $team)
				{
					if  ($round->hasTeam($team))
					{
						// The team is already playing
						continue;
					}
					// Team is available, set it as a referee
					$game->setReferee($team);
					if (!isset($refereeCounter[$team->getId()]))
					{
						$refereeCounter[$team->getId()] = 0;
					}
					$refereeCounter[$team->getId()]++;
					break;
				}
				if (!$game->getReferee())
				{
					// Default
					$game->setReferee($game->getTeam1());
				}
			}
		}
	}

	/**
	 * Dispatch games according to the round robin rule
	 */
	protected function dispatchRoundRobinGames(Phase $phase)
	{
		$eventGrounds = $phase->getEvent()->getGrounds();
		$emptyGames = [];

		/** @var ArrayCollection $pools */
		$pools = $phase->getPools();
		$poolsArray = $pools->toArray();

		// Loop while we have games to schedule
		while (!$phase->isFullyScheduled())
		{
			// Create a new game
			$newGame = $this->nextGameSlot($phase, $eventGrounds);

			// Order the pools from the lazyiest to the busyiest
			usort($poolsArray,
				function (Pool $a, Pool $b)
				{
					return $a->getScheduledRate() > $b->getScheduledRate();
				}
			);

			// Try to schedule games for the lazyiest pool first, then try busyier pools
			/** @var Pool $pool */
			foreach ($poolsArray as $pool)
			{
				if ($pool->getScheduledRate() >= 1)
				{
					// This pool is already fully scheduled
					continue;
				}

				// Populate the game with lazyiest teams
				$this->setLazyiestTeams($newGame, $pool);

				if ($newGame->getTeam1() && $newGame->getTeam2())
				{
					$pool->addGame($newGame);
					break;
				}
			}

			if (!$newGame->getTeam1() || !$newGame->getTeam2())
			{
				$emptyGames[] = $newGame;
			}
		}

		// Cleanup of game slots that could not be filled
		foreach ($emptyGames as $emptyGame)
		{
			$emptyGame->getRound()->removeGame($emptyGame);
		}
	}

	/**
	 * Dispatch games according to the brackets rule
	 * @param Phase $phase
	 */
	protected function dispatchBracketGames(Phase $phase)
	{
		$eventGrounds = $phase->getEvent()->getGrounds();

		do
		{
			$areWeDone = true;
			foreach ($phase->getPools() as $pool)
			{
				$game = $this->getNextBracketGame($pool->getGames()->toArray(), $pool->getTeams()->toArray());
				if ($game)
				{
					$newGame = $this->nextGameSlot($phase, $eventGrounds);
					$newGame->setTeam1($game->getTeam1());
					$newGame->setTeam2($game->getTeam2());
					$pool->addGame($newGame);
					$areWeDone = false;
				}
			}
		}
		while (!$areWeDone);
	}

	protected function dispatchCumulativeRankGames(Phase $phase)
	{
		$eventGrounds = $phase->getEvent()->getGrounds();
		$emptyGames = [];

		$pools = $phase->getPools();
		$poolsArray = $pools->toArray();
		$targetScheduledRate = floor(end($poolsArray)->getScheduledRate()) + 1;

		do
		{
			$areWeDone = true;
			// Create a new game
			$newGame = $this->nextGameSlot($phase, $eventGrounds);

			// Order the pools from the lazyiest to the busyiest
			/** @var ArrayCollection $pools */
			usort($poolsArray,
				function (Pool $a, Pool $b)
				{
					return $a->getScheduledRate() > $b->getScheduledRate();
				}
			);

			// Try to schedule games for the lazyiest pool first, then try busyier pools
			/** @var Pool $pool */
			foreach ($poolsArray as $pool)
			{
				if ($pool->getScheduledRate() >= $targetScheduledRate || count($pool->getTeams()) <= 1)
				{
					// This pool is already fully scheduled
					continue;
				}
				$areWeDone = false;

				// Populate the game with lazyiest teams
				$this->setLazyiestTeams($newGame, $pool);

				if ($newGame->getTeam1() && $newGame->getTeam2())
				{
					$pool->addGame($newGame);
					break;
				}
			}

			if (!$newGame->getTeam1() || !$newGame->getTeam2())
			{
				$emptyGames[] = $newGame;
			}
		}
		while (!$areWeDone);

		// Cleanup of game slots that could not be filled
		foreach ($emptyGames as $emptyGame)
		{
			$emptyGame->getRound()->removeGame($emptyGame);
		}

	}

	/**
	 * Get the next unscheduled game according to bracket rules
	 *
	 * @param Game[] $existingGames
	 * @param Team[] $selectedTeams
	 * @return Game|null
	 */
	public function getNextBracketGame(array $existingGames, array $selectedTeams)
	{
		$nextSelectedTeams = [];
		while (count($selectedTeams) >= 2)
		{
			$team1 = array_shift($selectedTeams);
			$team2 = array_shift($selectedTeams);

			$exists = false;
			$game = null;

			// Search this game of team1 Vs team2 in existing games
			foreach ($existingGames as $game)
			{
				if (($game->getTeam1() == $team1 && $game->getTeam2() == $team2) ||
				    ($game->getTeam1() == $team2 && $game->getTeam2() == $team1))
				{
					$exists = true;

					if ($game->isPlayed())
					{
						$winner = $game->getScore1() > $game->getScore2() ? $game->getTeam1() : $game->getTeam2();
						$nextSelectedTeams[] = $winner;
					}

					// Found the game
					break;
				}

			}
			if (!$exists)
			{
				$game = new Game();
				$game->setTeam1($team1);
				$game->setTeam2($team2);
				// We are done, this is a new unscheduled bracket game
				return $game;
			}
		}

		// All games are scheduled at this level
		if (count($nextSelectedTeams) >= 2)
		{
			// Go search for a game at the next level
			return $this->getNextBracketGame($existingGames, $nextSelectedTeams);
		}
		else
		{
			return null;
		}
	}

	/**
	 * Return a Game object pointing at the next available ground in the next available round
	 * it may create a new Round if all rounds are complete
	 *
	 * @param Phase $phase
	 * @param Collection $eventGrounds
	 * @return Game
	 */
	private function nextGameSlot(Phase $phase, Collection $eventGrounds)
	{
		// Get the first incomplete round
		$newGame = new Game();
		$roundNumber = 0;
		foreach ($phase->getRounds() as $existingRound)
		{
			$roundNumber = $existingRound->getNumber();
			$takenGrounds = new ArrayCollection();
			foreach ($existingRound->getGames() as $existingGame)
			{
				$takenGrounds->add($existingGame->getGround());
			}
			// Search for a free ground
			foreach ($eventGrounds as $eventGround)
			{
				if (!$takenGrounds->contains($eventGround))
				{
					// We found a free ground in an incomplete round
					$existingRound->addGame($newGame);
					$newGame->setGround($eventGround);
					return $newGame;
				}
			}
		}
		// If no free ground in incomplete round, create a new round
		$round = new Round();
		$round->setNumber($roundNumber + 1);
		$round->addGame($newGame);
		$phase->addRound($round);
		$newGame->setGround($eventGrounds[0]);
		return $newGame;
	}

	/**
	 * Populate $newGame with the lazyiest teams in $pool
	 *
	 * @param Game $newGame
	 * @param Pool $pool
	 */
	private function setLazyiestTeams(Game $newGame, Pool $pool)
	{
		// Order the teams from the lazyiest to the busyiest
		$teams = $pool->getTeams()->toArray();
		usort($teams,
			function (Team $a, Team $b) use ($pool)
			{
				if ($pool->getTeamNbParticipations($a) == $pool->getTeamNbParticipations($b))
				{
					// In this case, we try to match the worst teams first (for CumulativeRank game)
					foreach ($this->ranker->getPoolRanks($pool) as $teamId => $rank)
					{
						if ($teamId == $a->getId())
						{
							return 1;
						}
						if ($teamId == $b->getId())
						{
							return -1;
						}
					}
				}
				return $pool->getTeamNbParticipations($a) > $pool->getTeamNbParticipations($b);
			}
		);

		// Try to schedule the lazyiest team first
		foreach ($teams as $team1)
		{
			// Is the team already playing in this round ?
			if ($newGame->getRound()->hasTeam($team1))
			{
				continue;
			}
			foreach ($teams as $team2)
			{
				if ($team1 == $team2 || $newGame->getRound()->hasTeam($team2))
				{
					continue;
				}
				if ($pool->hasGame($team1, $team2) && $pool->getPhase()->getRule() != Rule::CUMULATIVERANK)
				{
					continue;
				}
				// We found a viable match!
				$newGame->setTeam1($team1);
				$newGame->setTeam2($team2);
				return;
			}
		}
	}

	public function removeTeamFromPool(Pool $pool, Team $team)
	{
		$isClear = true;
		// Delete the games where this team plays
		/** @var Game $game */
		foreach ($pool->getGames() as $game)
		{
			if ($game->hasTeam($team))
			{
				if ($game->isPlayed())
				{
					$isClear = false;
				}
				else
				{
					$pool->removeGame($game);
					$this->em->remove($game);
				}
			}
		}
		// If the team is not playing anymore, remove it from the pool
		if ($isClear)
		{
			$pool->removeTeam($team);
		}
		$this->em->flush();
		return $isClear;
	}

	public function removeTeamFromPhase(Phase $phase, Team $team)
	{
		$isClear = true;
		foreach ($phase->getPools() as $pool)
		{
			$isClear &= $this->removeTeamFromPool($pool, $team);
		}
		if ($isClear)
		{
			$phase->removeTeam($team);
		}
		$this->em->flush();
		return $isClear;
	}

	public function removeTeamFromEvent(Team $team)
	{
		$isClear = true;
		foreach ($team->getEvent()->getPhases() as $phase)
		{
			$isClear &= $this->removeTeamFromPhase($phase, $team);
		}
		if ($isClear)
		{
			$team->getEvent()->removeTeam($team);
			$this->em->remove($team);
		}
		$this->em->flush();
		return $isClear;
	}

	public function removePoolFromPhase(Pool $pool)
	{
		$isClear = true;
		/** @var Game $game */
		foreach($pool->getGames() as $game)
		{
			if ($game->isPlayed())
			{
				$isClear = false;
			}
			else
			{
				$pool->removeGame($game);
				$this->em->remove($game);
			}
		}
		if ($isClear)
		{
			foreach ($pool->getTeams() as $team)
			{
				$pool->removeTeam($team);
			}
			$pool->getPhase()->removePool($pool);
			$this->em->flush();
			$this->em->remove($pool);
		}
		$this->em->flush();
		return $isClear;
	}
}
