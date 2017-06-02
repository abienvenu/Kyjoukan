<?php

namespace Abienvenu\KyjoukanBundle\Service;

use Abienvenu\KyjoukanBundle\Entity\Game;
use Abienvenu\KyjoukanBundle\Entity\Pool;
use Abienvenu\KyjoukanBundle\Entity\Team;

class RankService
{
	public function getPoolRanks(Pool $pool)
	{
		$rankings = [];
		/** @var Team $team */
		foreach ($pool->getTeams() as $team)
		{
			$rankings[$team->getId()] = ["team" => $team->getName(), "win" => 0, "played" => 0, "loose" => 0, "advantage" => 0, "points" => 0];
		}

		/** @var Game $game */
		foreach ($pool->getGames() as $game)
		{
			if ($game->getScore1() || $game->getScore2())
			{
				if ($game->getScore1() > $game->getScore2())
				{
					$rankings[$game->getTeam1()->getId()]["win"]++;
					$rankings[$game->getTeam2()->getId()]["loose"]++;
				}
				else if ($game->getScore1() < $game->getScore2())
				{
					$rankings[$game->getTeam1()->getId()]["loose"]++;
					$rankings[$game->getTeam2()->getId()]["win"]++;
				}
				else
				{
					$rankings[$game->getTeam1()->getId()]["loose"] += 0.5;
					$rankings[$game->getTeam1()->getId()]["win"] += 0.5;
					$rankings[$game->getTeam2()->getId()]["loose"] += 0.5;
					$rankings[$game->getTeam2()->getId()]["win"] += 0.5;
				}
				$rankings[$game->getTeam1()->getId()]["played"]++;
				$rankings[$game->getTeam2()->getId()]["played"]++;
				$rankings[$game->getTeam1()->getId()]["advantage"] += $game->getScore1() - $game->getScore2();
				$rankings[$game->getTeam2()->getId()]["advantage"] += $game->getScore2() - $game->getScore1();
				$rankings[$game->getTeam1()->getId()]["points"] += $game->getScore1();
				$rankings[$game->getTeam2()->getId()]["points"] += $game->getScore2();
			}
		}

		uasort($rankings, function (array $rank1, array $rank2)
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
