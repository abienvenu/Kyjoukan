<?php

namespace Abienvenu\KyjoukanBundle\Service;

use Abienvenu\KyjoukanBundle\Entity\Game;
use Abienvenu\KyjoukanBundle\Entity\Phase;
use Abienvenu\KyjoukanBundle\Entity\Pool;
use Abienvenu\KyjoukanBundle\Entity\Round;
use Doctrine\Common\Collections\ArrayCollection;
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
				$teams = $pool->getTeams();
				if ($pool->getScheduledRate() < 1)
				{
					// Get the first incomplete round
					$round = null;
					$ground = null;
					$roundNumber = 0;
					foreach ($phase->getRounds() as $existingRound)
					{
						$roundNumber = $existingRound->getNumber();
						$takenGrounds = new ArrayCollection();
						foreach ($existingRound->getGames() as $game)
						{
							$takenGrounds->add($game->getGround());
						}
						// Search for a free ground
						foreach ($eventGrounds as $eventGround)
						{
							if (!$takenGrounds->contains($eventGround))
							{
								// We found a free ground in an incomplete round
								$round = $existingRound;
								$ground = $eventGround;
								break;
							}
						}
						if ($round)
						{
							break;
						}
					}
					// If no free ground in incomplete round, create a new round
					if (!$round)
					{
						$round = new Round();
						$round->setNumber($roundNumber + 1);
						$phase->addRound($round);
						$ground = $eventGrounds[0];
					}

					// Create a new game
					$game = new Game();
					$game->setTeam1($teams[0]);
					$game->setTeam2($teams[1]);
					$game->setGround($ground);
					$round->addGame($game);
					$pool->addGame($game);
					break;
				}
			}
		}

		$this->em->flush();
	}
}
