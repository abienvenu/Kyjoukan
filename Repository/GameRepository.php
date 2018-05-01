<?php

namespace Abienvenu\KyjoukanBundle\Repository;

use Abienvenu\KyjoukanBundle\Entity\Phase;
use Doctrine\ORM\EntityRepository;

class GameRepository extends EntityRepository
{
	public function findByPhase(Phase $phase)
	{
		$dql = "
			SELECT game
			FROM KyjoukanBundle:Game game
			JOIN game.pool pool
			JOIN game.ground ground
			JOIN game.round round
			WHERE pool.phase = :phase
			ORDER BY round.number, ground.name";
		$query = $this->_em->createQuery($dql)->setParameter('phase', $phase);
		return $query->getResult();
	}
}
