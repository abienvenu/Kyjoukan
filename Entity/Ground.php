<?php

namespace Abienvenu\KyjoukanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ground")
 * @ORM\Entity(repositoryClass="Abienvenu\KyjoukanBundle\Repository\GroundRepository")
 */
class Ground
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	private $name;

	/**
	 * @ORM\ManyToOne(targetEntity="Event", inversedBy="grounds")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $event;

	public function getId() : int
	{
		return $this->id;
	}

	public function setName(string $name) : Ground
	{
		$this->name = $name;
		return $this;
	}

	public function getName() : ?string
	{
		return $this->name;
	}

	public function setEvent(Event $event) : Ground
	{
		$this->event = $event;
		return $this;
	}

	public function getEvent() : Event
	{
		return $this->event;
	}

	/**
	 * Returns the number of times the given pool has played on this ground
	 */
	public function countPool(Pool $pool) : int
	{
		$nb = 0;
		/** @var Game $game */
		foreach ($pool->getGames() as $game)
		{
			if ($game->getGround()->getId() == $this->getId())
			{
				$nb++;
			}
		}
		return $nb;
	}

	/**
	 * Returns the number of times the given team has played on this ground across all phases
	 */
	public function countTeam(Team $team) : int
	{
		$nb = 0;
		/** @var Phase $phase */
		foreach ($this->getEvent()->getPhases() as $phase)
		{
			foreach ($phase->getRounds() as $round)
			{
				foreach ($round->getGames() as $game)
				{
					if ($game->getTeam1() && $game->getTeam2())
					{
						if ($game->getTeam1()->getId() == $team->getId() || $game->getTeam2()->getId() == $team->getId())
						{
							$nb++;
						}
					}
				}
			}
		}
		return $nb;
	}
}
