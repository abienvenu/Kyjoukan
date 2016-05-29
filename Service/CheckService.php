<?php

namespace Abienvenu\KyjoukanBundle\Service;

use Abienvenu\KyjoukanBundle\Entity\Phase;
use Abienvenu\KyjoukanBundle\Entity\Team;
use Doctrine\ORM\EntityManager;

class CheckService
{
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function checkPhasePools(Phase $phase)
	{
		$errors = [];
		// Check at least one pool exists
		if (count($phase->getPools()) == 0)
		{
			$errors[] = "Veuillez créer au moins un groupe";
		}
		else
		{
			// Check every team is dispatched once and only once
			/** @var Team $team */
			foreach ($phase->getTeams() as $team)
			{
				$nbPooled = 0;
				foreach ($phase->getPools() as $pool)
				{
					if ($pool->hasTeam($team))
					{
						$nbPooled++;
					}
				}

				if ($nbPooled == 0)
				{
					$errors[] = "L'équipe {$team->getName()} n'est dans aucun groupe";
				}
				if ($nbPooled > 1)
				{
					$errors[] = "L'équipe {$team->getName()} est dans plusieurs groupes";
				}
			}
		}

		return implode("<br />", $errors);
	}
}
