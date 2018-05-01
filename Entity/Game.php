<?php

namespace Abienvenu\KyjoukanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
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
	 * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $referee = null;

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

	public function getId() : int
	{
		return $this->id;
	}

	public function setTeam1($team1) : Game
	{
		$this->team1 = $team1;
		return $this;
	}

	public function getTeam1() : ?Team
	{
		return $this->team1;
	}

	public function setTeam2($team2) : Game
	{
		$this->team2 = $team2;
		return $this;
	}

	public function getTeam2() : ?Team
	{
		return $this->team2;
	}

	public function setReferee(Team $referee) : Game
	{
		$this->referee = $referee;
		return $this;
	}

	public function getReferee() : ?Team
	{
		return $this->referee;
	}

	public function setGround(Ground $ground) : Game
	{
		$this->ground = $ground;
		return $this;
	}

	public function getGround() : ?Ground
	{
		return $this->ground;
	}

	public function setScore1(int $score1) : Game
	{
		$this->score1 = $score1;
		return $this;
	}

	public function getScore1() : ?int
	{
		return $this->score1;
	}

	public function setScore2(int $score2) : Game
	{
		$this->score2 = $score2;
		return $this;
	}

	public function getScore2() : ?int
	{
		return $this->score2;
	}

	public function setPool(Pool $pool) : Game
	{
		$this->pool = $pool;
		return $this;
	}

	public function getPool() : Pool
	{
		return $this->pool;
	}

	public function setRound(Round $round) : Game
	{
		$this->round = $round;
		return $this;
	}

	public function getRound() : ?Round
	{
		return $this->round;
	}

	/**
	 * Tell if a Team is involved in this Game
	 */
	public function hasTeam(Team $team) : bool
	{
		return $this->getTeam1() === $team || $this->getTeam2() === $team || $this->getReferee() === $team;
	}

	/**
	 * Tell if this Game is already played
	 */
	public function isPlayed() : bool
	{
		return $this->getScore1() || $this->getScore2();
	}
}
