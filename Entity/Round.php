<?php

namespace Abienvenu\KyjoukanBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="round")
 * @ORM\Entity(repositoryClass="Abienvenu\KyjoukanBundle\Repository\RoundRepository")
 */
class Round
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
	 * @var int
	 *
	 * @ORM\Column(name="number", type="integer")
	 */
	private $number;

	/**
	 * @ORM\ManyToOne(targetEntity="Phase", inversedBy="rounds")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $phase;

	/**
	 * @ORM\OneToMany(targetEntity="Game", mappedBy="round", cascade={"persist"})
	 */
	private $games;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->games = new ArrayCollection();
	}


	public function getId() : int
	{
		return $this->id;
	}

	public function setNumber(int $number) : Round
	{
		$this->number = $number;

		return $this;
	}

	public function getNumber() : ?int
	{
		return $this->number;
	}

	public function setPhase(Phase $phase) : Round
	{
		$this->phase = $phase;
		return $this;
	}

	public function getPhase() : Phase
	{
		return $this->phase;
	}

	public function addGame(Game $game) : Round
	{
		$this->games[] = $game;
		$game->setRound($this);
		return $this;
	}

	public function removeGame(Game $game)
	{
		$this->games->removeElement($game);
	}

	/**
	 * @return Game[]
	 */
	public function getGames() : Collection
	{
		return $this->games;
	}

	/**
	 * Determine if the Team is already scheduled during this Round
	 */
	public function hasTeam(Team $team) : bool
	{
		foreach ($this->getGames() as $game)
		{
			if ($game->getTeam1() === $team || $game->getTeam2() === $team || $game->getReferee() === $team)
			{
				return true;
			}
		}
		return false;
	}
}
