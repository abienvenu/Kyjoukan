<?php

namespace Abienvenu\KyjoukanBundle\Service;

use Abienvenu\KyjoukanBundle\Entity\Game;
use Abienvenu\KyjoukanBundle\Entity\Phase;
use Abienvenu\KyjoukanBundle\Entity\Team;

class RankService
{
	public function getPhaseRanks(Phase $phase)
	{
		$rankings = [];
		/** @var Team $team */
		foreach ($phase->getTeams() as $team)
		{
			$rankings[$team->getId()] = ["team" => $team->getName(), "win" => 0, "played" => 0, "loose" => 0, "advantage" => 0, "points" => 0];
		}

		foreach ($phase->getPools() as $pool)
		{
			/** @var Game $game */
			foreach ($pool->getGames() as $game)
			{
				if ($game->getScore1() && $game->getScore2())
				{
					if ($game->getScore1() >= $game->getScore2())
					{
						$rankings[$game->getTeam1()->getId()]["win"]++;
						$rankings[$game->getTeam2()->getId()]["loose"]++;
					}
					else
					{
						$rankings[$game->getTeam1()->getId()]["loose"]++;
						$rankings[$game->getTeam2()->getId()]["win"]++;
					}
					$rankings[$game->getTeam1()->getId()]["played"]++;
					$rankings[$game->getTeam2()->getId()]["played"]++;
					$rankings[$game->getTeam1()->getId()]["advantage"] += $game->getScore1() - $game->getScore2();
					$rankings[$game->getTeam2()->getId()]["advantage"] += $game->getScore2() - $game->getScore1();
					$rankings[$game->getTeam1()->getId()]["points"] += $game->getScore1();
					$rankings[$game->getTeam2()->getId()]["points"] += $game->getScore2();
				}
			}
		}

		usort($rankings, function (array $rank1, array $rank2)
			{
				foreach (["win", "advantage", "points", "played"] as $criteria)
				{
					if ($rank1[$criteria] > $rank2[$criteria])
					{
						return -1;
					}
					if ($rank1[$criteria] < $rank2[$criteria])
					{
						return 1;
					}
				}
				return -1;
			}
		);

		return $rankings;
	}
}