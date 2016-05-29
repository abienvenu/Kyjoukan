<?php

namespace Abienvenu\KyjoukanBundle\Repository;

use Abienvenu\KyjoukanBundle\Entity\Event;
use Abienvenu\KyjoukanBundle\Entity\Phase;
use Abienvenu\KyjoukanBundle\Entity\Pool;
use Doctrine\ORM\EntityRepository;

class TeamRepository extends EntityRepository
{
	public function getTeamsForPhase(Phase $phase)
	{
		// Find the teams that are in the given phase
		return $this->createQueryBuilder('t')
		            ->join('t.event', 'e')
					->join('e.phases', 'p', 'WITH', 'p = :phase')
					->join('p.teams', 't2', 'WITH', 't2 = t')
		            ->setParameter('phase', $phase);
	}

	public function getTeamsForEvent(Event $event)
	{
		// Find the teams that are in the given event
		return $this->createQueryBuilder('t')
		            ->join('t.event', 'e', 'WITH', 'e = :event')
		            ->setParameter('event', $event);
	}

	public function getTeamsForPool(Pool $pool)
	{
		// Find the teams that are in the given Pool
		// Find the teams that are in the given phase
		return $this->createQueryBuilder('t')
		            ->join('t.event', 'e')
					->join('e.phases', 'p')
		            ->join('p.pools', 'pool', 'WITH', 'pool = :pool')
		            ->join('pool.teams', 't2', 'WITH', 't2 = t')
		            ->setParameter('pool', $pool);

	}
}
