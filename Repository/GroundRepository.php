<?php

namespace Abienvenu\KyjoukanBundle\Repository;

use Abienvenu\KyjoukanBundle\Entity\Event;
use Doctrine\ORM\EntityRepository;

class GroundRepository extends EntityRepository
{
	public function getGroundsForEvent(Event $event)
	{
		return $this->createQueryBuilder('g')
			->where('g.event = :event')
			->setParameter('event', $event);
	}
}
