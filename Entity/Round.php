<?php

namespace Abienvenu\KyjoukanBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Round
 *
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
	 * @ORM\OrderBy({"ground" = "asc"})
	 */
	private $games;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->games = new ArrayCollection();
	}


	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set number
	 *
	 * @param integer $number
	 * @return Round
	 */
	public function setNumber($number)
	{
		$this->number = $number;

		return $this;
	}

	/**
	 * Get number
	 *
	 * @return integer
	 */
	public function getNumber()
	{
		return $this->number;
	}

	/**
	 * Set phase
	 *
	 * @param Phase $phase
	 * @return Pool
	 */
	public function setPhase($phase)
	{
		$this->phase = $phase;

		return $this;
	}

	/**
	 * Get phase
	 *
	 * @return Phase
	 */
	public function getPhase()
	{
		return $this->phase;
	}

	/**
	 * Add games
	 *
	 * @param Game $game
	 * @return Pool
	 */
	public function addGame(Game $game)
	{
		$this->games[] = $game;
		$game->setRound($this);

		return $this;
	}

	/**
	 * Remove games
	 *
	 * @param Game $game
	 */
	public function removeGame(Game $game)
	{
		$this->games->removeElement($game);
	}

	/**
	 * Get games
	 *
	 * @return Game[]
	 */
	public function getGames()
	{
		return $this->games;
	}

	/**
	 * Determine if the Team is already scheduled during this Round
	 *
	 * @param Team $team
	 * @return bool
	 */
	public function hasTeam(Team $team)
	{
		foreach ($this->getGames() as $game)
		{
			if ($game->getTeam1() == $team || $game->getTeam2() == $team)
			{
				return true;
			}
		}
		return false;
	}
}
