<?php

namespace Abienvenu\KyjoukanBundle\Entity;

use Abienvenu\KyjoukanBundle\Enum\Rule;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Pool
 *
 * @ORM\Table(name="pool")
 * @ORM\Entity(repositoryClass="Abienvenu\KyjoukanBundle\Repository\PoolRepository")
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
	    $game->setPool($this);

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
	 * Add team
	 *
	 * @param Team $team
	 * @return Phase
	 */
	public function addTeam(Team $team)
	{
		$this->teams[] = $team;
		return $this;
	}

	/**
	 * Remove team
	 *
	 * @param Team $team
	 */
	public function removeTeam(Team $team)
	{
		$this->teams->removeElement($team);
	}

	/**
	 * Get teams
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getTeams()
	{
		return $this->teams;
	}

	/**
	 * Test if the Team is already registered in the Pool
	 *
	 * @param Team $team
	 * @return bool
	 */
	public function hasTeam(Team $team)
	{
		return $this->getTeams()->contains($team);
	}

	/**
	 * Tell how much games are scheduled
	 *
	 * @return mixed 0 if no game is scheduled, 1 if all games are scheduled
	 * @throws \Exception
	 */
	public function getScheduledRate()
	{
		$rule = $this->getPhase()->getRule();
		switch ($rule)
		{
			case Rule::ROUNDROBIN:
				$nbTeams = count($this->getTeams());
				$nbTotalGames = $nbTeams * ($nbTeams - 1) / 2;
				break;
			default:
				throw new \Exception("Unknown rule: $rule");
		}

		return (count($this->getGames()) + 1) / ($nbTotalGames + 1);
	}

	/**
	 * Return the number of participations of the given team
	 *
	 * @param Team $team
	 * @return int
	 */
	public function getTeamNbParticipations(Team $team)
	{
		$nb = 0;
		foreach ($this->getGames() as $game)
		{
			if ($game->getTeam1() == $team || $game->getTeam2() == $team)
			{
				$nb++;
			}
		}
		return $nb;
	}

	public function hasGame(Team $team1, Team $team2)
	{
		foreach ($this->getGames() as $game)
		{
			if (($game->getTeam1() == $team1 && $game->getTeam2() == $team2) ||
			    ($game->getTeam1() == $team2 && $game->getTeam2() == $team1))
			{
				return true;
			}
		}
		return false;
	}
}
