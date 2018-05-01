<?php

namespace Abienvenu\KyjoukanBundle\Service;

use Abienvenu\KyjoukanBundle\Entity\Phase;
use Abienvenu\KyjoukanBundle\Entity\Team;
use Abienvenu\KyjoukanBundle\Enum\Rule;

class CheckService
{
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

		return $errors;
	}

	public function checkPhaseGames(Phase $phase)
	{
		$errors = ['info' => [], 'warning' => [], 'danger' => []];
		// Check a team does not play on several ground at the same time
		foreach ($phase->getRounds() as $round)
		{
			$playingTeams = [];
			$refereeTeams = [];
			foreach ($round->getGames() as $game)
			{
				if (in_array($game->getTeam1(), $playingTeams))
				{
					$errors['danger'][] = "L'équipe {$game->getTeam1()->getName()} est à deux endroits dans le round {$round->getNumber()}";
				}
				$playingTeams[] = $game->getTeam1();
				if (in_array($game->getTeam2(), $playingTeams))
				{
					$errors['danger'][] = "L'équipe {$game->getTeam2()->getName()} est à deux endroits dans le round {$round->getNumber()}";
				}
				$playingTeams[] = $game->getTeam2();

				if (in_array($game->getReferee(), $playingTeams))
				{
					$errors['warning'][] = "L'arbitre {$game->getReferee()->getName()} joue déjà dans le round {$round->getNumber()}";
				}
				if (in_array($game->getReferee(), $refereeTeams))
				{
					$errors['info'][] = "L'arbitre {$game->getReferee()->getName()} arbitre déjà dans le round {$round->getNumber()}";
				}
				$refereeTeams[] = $game->getReferee();

				// Check all teams belong to the same pool
				if (!$game->getPool()->hasTeam($game->getTeam1()))
				{
					$errors['danger'][] = "L'équipe {$game->getTeam1()->getName()} est programmée sur match du groupe {$game->getPool()->getName()} dans le round {$round->getNumber()}";
				}
				if (!$game->getPool()->hasTeam($game->getTeam2()))
				{
					$errors['danger'][] = "L'équipe {$game->getTeam2()->getName()} est programmée sur match du groupe {$game->getPool()->getName()} dans le round {$round->getNumber()}";
				}
				if (!$game->getPool()->hasTeam($game->getReferee()))
				{
					$errors['info'][] = "L'équipe {$game->getReferee()->getName()} arbitre un match du groupe {$game->getPool()->getName()} dans le round {$round->getNumber()}";
				}
			}
		}

		// Check all pools are fully scheduled
		foreach ($phase->getPools() as $pool)
		{
			$scheduledRate = $pool->getScheduledRate();
			if ($scheduledRate < 1)
			{
				$errors['warning'][] = "Il manque des matchs dans le groupe {$pool->getName()}";
			}
			if ($scheduledRate > 1 && $phase->getRule() != Rule::CUMULATIVERANK)
			{
				$errors['danger'][] = "Il y a des matchs en trop dans le groupe {$pool->getName()}";
			}
		}

		return $errors;
	}

	public function checkPhaseTeams(Phase $phase)
	{
		$errors = [];
		if (count($phase->getTeams()))
		{
			foreach ($phase->getEvent()->getTeams() as $team)
			{
				if (!$phase->hasTeam($team))
				{
					$errors[] = "L'équipe {$team->getName()} est exclue de cette phase";
				}
			}
		}
		else
		{
			$errors[] = "Aucune équipe n'est chargée dans cette phase. Utilisez le bouton \"Chargez les équipes de l'évènement\" pour recopier toutes les équipes de l'évènement dans cette phase";
		}
		return $errors;
	}
}
