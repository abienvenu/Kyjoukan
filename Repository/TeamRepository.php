<?php

namespace Abienvenu\KyjoukanBundle\Repository;

use Abienvenu\KyjoukanBundle\Entity\Phase;
use Doctrine\ORM\EntityRepository;

class TeamRepository extends EntityRepository
{
	public function getTeamsForPhase(Phase $phase)
	{
		// Find the teams that are in the given phase
		return $this->createQueryBuilder('t')
		            ->join('t.event', 'e')
					->join('e.phases', 'p', 'WITH', 'p.id = :phase')
					->join('p.teams', 't2', 'WITH', 't2 = t')
		            ->setParameter('phase', $phase->getId());
	}
}
