<?php

namespace Abienvenu\KyjoukanBundle\Repository;

use Abienvenu\KyjoukanBundle\Entity\Phase;
use Doctrine\ORM\EntityRepository;

class RoundRepository extends EntityRepository
{
	public function getRoundsForPhase(Phase $phase)
	{
		return $this->createQueryBuilder('r')
					->where('r.phase = :phase')
		            ->setParameter('phase', $phase);
	}
}
