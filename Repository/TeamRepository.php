<?php

namespace Abienvenu\KyjoukanBundle\Repository;

use Abienvenu\KyjoukanBundle\Entity\Phase;
use Doctrine\ORM\EntityRepository;

class TeamRepository extends EntityRepository
{
	public function getTeamsForPhase(Phase $phase)
	{
		return $this->createQueryBuilder('t')
		            ->where('t.phase = :phase')
		            ->setParameter('phase', $phase);
	}
}
