<?php

namespace Abienvenu\KyjoukanBundle\Service;

use Abienvenu\KyjoukanBundle\Entity\Game;
use Abienvenu\KyjoukanBundle\Entity\Phase;
use Abienvenu\KyjoukanBundle\Entity\Pool;
use Abienvenu\KyjoukanBundle\Entity\Round;
use Abienvenu\KyjoukanBundle\Entity\Team;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;

class DispatcherService
{
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
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
				$pool->addTeam($team);
				$dispatched++;
			}
		}
		$this->em->flush();
		return $dispatched;
	}

	/**
	 * Dispatch all the games into the phase, for every pool
	 *
	 * @param Phase $phase
	 */
	public function shuffleGames(Phase $phase)
	{
		// Delete all games that have not been played
		foreach ($phase->getPools() as $pool)
		{
			foreach ($pool->getGames() as $game)
			{
				if (!$game->getScore1() && !$game->getScore2())
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

		$eventGrounds = $phase->getEvent()->getGrounds();

		// Loop while we have games to schedule
		while (!$phase->isFullyScheduled())
		{
			// Create a new game
			$newGame = $this->nextGameSlot($phase, $eventGrounds);

			// Order the pools from the lazyiest to the busyiest
			/** @var ArrayCollection $pools */
			$pools = $phase->getPools();
			$poolsArray = $pools->toArray();
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
		}

		// The phase is fully scheduled with playing teams
		// Now let's find referees for each game
		$refereeCounter = [];
		foreach ($phase->getRounds() as $round)
		{
			foreach ($round->getGames() as $game)
			{
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
			}
		}

		$this->em->flush();
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
				if ($pool->hasGame($team1, $team2))
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
}
