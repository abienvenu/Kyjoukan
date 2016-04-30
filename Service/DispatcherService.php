<?php

namespace Abienvenu\KyjoukanBundle\Service;

use Abienvenu\KyjoukanBundle\Entity\Phase;
use Abienvenu\KyjoukanBundle\Entity\Team;
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
}
