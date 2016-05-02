<?php

namespace Abienvenu\KyjoukanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="Abienvenu\KyjoukanBundle\Repository\GameRepository")
 */
class Game
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
	 * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $team1 = null;

	/**
	 * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $team2 = null;

	/**
	 * @ORM\ManyToOne(targetEntity="Ground")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $ground = null;

	/**
	 * @ORM\ManyToOne(targetEntity="Pool", inversedBy="games")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $pool;

	/**
	 * @ORM\ManyToOne(targetEntity="Round", inversedBy="games")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $round = null;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="score1", type="integer", nullable=true)
	 */
	private $score1;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="score2", type="integer", nullable=true)
	 */
	private $score2;

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
	 * Set team1
	 *
	 * @param Team $team1
	 * @return Game
	 */
	public function setTeam1($team1)
	{
		$this->team1 = $team1;

		return $this;
	}

	/**
	 * Get team1
	 *
	 * @return Team
	 */
	public function getTeam1()
	{
		return $this->team1;
	}

	/**
	 * Set team2
	 *
	 * @param Team $team2
	 * @return Game
	 */
	public function setTeam2($team2)
	{
		$this->team2 = $team2;

		return $this;
	}

	/**
	 * Get team2
	 *
	 * @return Team
	 */
	public function getTeam2()
	{
		return $this->team2;
	}

	/**
	 * Set ground
	 *
	 * @param Ground $ground
	 * @return Game
	 */
	public function setGround($ground)
	{
		$this->ground = $ground;

		return $this;
	}

	/**
	 * Get ground
	 *
	 * @return Ground
	 */
	public function getGround()
	{
		return $this->ground;
	}

	/**
	 * Set score1
	 *
	 * @param integer $score1
	 * @return Game
	 */
	public function setScore1($score1)
	{
		$this->score1 = $score1;

		return $this;
	}

	/**
	 * Get score1
	 *
	 * @return integer
	 */
	public function getScore1()
	{
		return $this->score1;
	}

	/**
	 * Set score2
	 *
	 * @param integer $score2
	 * @return Game
	 */
	public function setScore2($score2)
	{
		$this->score2 = $score2;

		return $this;
	}

	/**
	 * Get score2
	 *
	 * @return integer
	 */
	public function getScore2()
	{
		return $this->score2;
	}

	/**
	 * Set pool
	 *
	 * @param Pool $pool
	 * @return Game
	 */
	public function setPool(Pool $pool)
	{
		$this->pool = $pool;

		return $this;
	}

	/**
	 * Get pool
	 *
	 * @return Pool
	 */
	public function getPool()
	{
		return $this->pool;
	}

	/**
	 * Set round
	 *
	 * @param Round $round
	 * @return Game
	 */
	public function setRound(Round $round)
	{
		$this->round = $round;

		return $this;
	}

	/**
	 * Get round
	 *
	 * @return Round
	 */
	public function getRound()
	{
		return $this->round;
	}
}
