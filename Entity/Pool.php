<?php

namespace Abienvenu\KyjoukanBundle\Entity;

use Abienvenu\KyjoukanBundle\Enum\Rule;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="pool")
 * @ORM\Entity()
 */
class Pool
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
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	private $name;

	/**
	 * @ORM\Column(name="color", type="string", length=255, nullable=true)
	 */
	private $color;

	/**
	 * @ORM\ManyToOne(targetEntity="Phase", inversedBy="pools")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $phase;

	/**
	 * @ORM\OneToMany(targetEntity="Game", mappedBy="pool", cascade={"persist"})
	 */
	private $games;

	/**
	 * @ORM\ManyToMany(targetEntity="Team")
	 */
	private $teams;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->games = new ArrayCollection();
		$this->teams = new ArrayCollection();
	}

	public function getId() : int
	{
		return $this->id;
	}

	public function getName() : ?string
	{
		return $this->name;
	}

	public function setName(string $name) : Pool
	{
		$this->name = $name;
		return $this;
	}

	public function getColor() : ?string
	{
		return $this->color;
	}

	public function setColor(string $color) : Pool
	{
		$this->color = $color;
		return $this;
	}

	public function setPhase(Phase $phase) : Pool
	{
		$this->phase = $phase;

		return $this;
	}

	public function getPhase() : Phase
	{
		return $this->phase;
	}

    public function addGame(Game $game) : Pool
    {
        $this->games[] = $game;
	    $game->setPool($this);
        return $this;
    }

    public function removeGame(Game $game)
    {
	    $this->games->removeElement($game);
    }

    public function getGames() : Collection
    {
        return $this->games;
    }

	public function addTeam(Team $team) : Pool
	{
		$this->teams[] = $team;
		return $this;
	}

	public function removeTeam(Team $team)
	{
		$this->teams->removeElement($team);
	}

	public function getTeams() : Collection
	{
		return $this->teams;
	}

	/**
	 * Test if the Team is already registered in the Pool
	 */
	public function hasTeam(Team $team) : bool
	{
		return $this->getTeams()->contains($team);
	}

	/**
	 * Tell how much games are scheduled
	 *
	 * @return float 0 if no game is scheduled, 1 if all games are scheduled
	 */
	public function getScheduledRate() : float
	{
		$rule = $this->getPhase()->getRule();
		$nbTeams = count($this->getTeams());
		switch ($rule)
		{
			case Rule::ROUNDROBIN:
				$nbTotalGames = $nbTeams * ($nbTeams - 1) / 2;
				break;
			case Rule::BRACKETS:
				// Schedule rate has no sense for brackets
				return 1;
			case Rule::CUMULATIVERANK:
				// For cumulative rank, the scheduled rate can go higher than 1
				$nbTotalGames = $nbTeams / 2;
				break;
			default:
				throw new \Exception("Unknown rule: $rule");
		}

		return count($this->getGames()) / max($nbTotalGames, 1);
	}

	/**
	 * Return the number of participations of the given team
	 */
	public function getTeamNbParticipations(Team $team) : int
	{
		$nb = 0;
		/** @var Game $game */
		foreach ($this->getGames() as $game)
		{
			if ($game->getTeam1() === $team || $game->getTeam2() === $team)
			{
				$nb++;
			}
		}
		return $nb;
	}

	public function hasGame(Team $team1, Team $team2)
	{
		/** @var Game $game */
		foreach ($this->getGames() as $game)
		{
			if (($game->getTeam1() === $team1 && $game->getTeam2() === $team2) ||
			    ($game->getTeam1() === $team2 && $game->getTeam2() === $team1))
			{
				return true;
			}
		}
		return false;
	}
}
