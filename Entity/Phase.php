<?php

namespace Abienvenu\KyjoukanBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Phase
 *
 * @ORM\Table(name="phase")
 * @ORM\Entity(repositoryClass="Abienvenu\KyjoukanBundle\Repository\PhaseRepository")
 */
class Phase
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
	 * @var int
	 *
	 * @ORM\Column(name="rule", type="integer")
	 */
	private $rule;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="start_date_time", type="datetime", nullable=true)
	 */
	private $startDateTime;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="round_duration", type="integer", nullable=true)
	 */
	private $roundDuration;

	/**
	 * @Gedmo\Slug(fields={"name"})
	 * @ORM\Column(length=128, unique=true)
	 */
	private $slug;

	/**
	 * @ORM\ManyToOne(targetEntity="Event", inversedBy="phases")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $event;

	/**
	 * @ORM\OneToMany(targetEntity="Pool", mappedBy="phase", cascade={"persist"})
	 */
	private $pools;

	/**
	 * @ORM\OneToMany(targetEntity="Round", mappedBy="phase", cascade={"persist"})
	 * @ORM\OrderBy({"number" = "ASC"})
	 */
	private $rounds;

	/**
	 * @ORM\ManyToMany(targetEntity="Team")
	 */
	private $teams;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->pools = new ArrayCollection();
		$this->rounds = new ArrayCollection();
		$this->teams= new ArrayCollection();
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
	 * Set name
	 *
	 * @param string $name
	 * @return Phase
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set rule
	 *
	 * @param integer $rule
	 * @return Phase
	 */
	public function setRule($rule)
	{
		$this->rule = $rule;

		return $this;
	}

	/**
	 * Get rule
	 *
	 * @return integer
	 */
	public function getRule()
	{
		return $this->rule;
	}

	/**
	 * @param \DateTime $startDateTime
	 * @return Phase
	 */
	public function setStartDateTime(\DateTime $startDateTime)
	{
		$this->startDateTime = $startDateTime;
		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getStartDateTime()
	{
		return $this->startDateTime;
	}

	/**
	 * @param int $roundDuration
	 * @return Phase
	 */
	public function setRoundDuration($roundDuration)
	{
		$this->roundDuration = $roundDuration;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRoundDuration()
	{
		return $this->roundDuration;
	}

	/**
	 * Set event
	 *
	 * @param Event $event
	 * @return Phase
	 */
	public function setEvent(Event $event)
	{
		$this->event = $event;

		return $this;
	}

	/**
	 * Get event
	 *
	 * @return Event
	 */
	public function getEvent()
	{
		return $this->event;
	}

	/**
	 * Add pool
	 *
	 * @param Pool $pool
	 * @return Phase
	 */
	public function addPool(Pool $pool)
	{
		$this->pools[] = $pool;
		$pool->setPhase($this);

		return $this;
	}

	/**
	 * Remove pool
	 *
	 * @param Pool $pool
	 */
	public function removePool(Pool $pool)
	{
		$this->pools->removeElement($pool);
	}

	/**
	 * Get pools
	 *
	 * @return Pool[]
	 */
	public function getPools()
	{
		return $this->pools;
	}

	/**
	 * Add round
	 *
	 * @param Round $round
	 * @return Phase
	 */
	public function addRound(Round $round)
	{
		$this->rounds[] = $round;
		$round->setPhase($this);

		return $this;
	}

	/**
	 * Remove round
	 *
	 * @param Round $round
	 */
	public function removeRound(Round $round)
	{
		$this->rounds->removeElement($round);
	}

	/**
	 * Get rounds
	 *
	 * @return Round[]
	 */
	public function getRounds()
	{
		return $this->rounds;
	}

	/**
	 * Set slug
	 *
	 * @param string $slug
	 * @return Phase
	 */
	public function setSlug($slug)
	{
		$this->slug = $slug;

		return $this;
	}

	/**
	 * Get slug
	 *
	 * @return string
	 */
	public function getSlug()
	{
		return $this->slug;
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
	 * Remove a team
	 *
	 * @param Team $team
	 * @return bool
	 */
	public function removeTeam(Team $team)
	{
		$this->teams->removeElement($team);
	}

	/**
	 * Get teams
	 *
	 * @return ArrayCollection
	 */
	public function getTeams()
	{
		return $this->teams;
	}

	/**
	 * Determine if the Team is already registered in the Phase
	 *
	 * @param Team $team
	 * @return bool
	 */
	public function hasTeam(Team $team)
	{
		return $this->getTeams()->contains($team);
	}

	/**
	 * Determine if the Team is inside a Pool of the Phase
	 *
	 * @param Team $team
	 * @return bool
	 */
	public function isTeamPooled(Team $team)
	{
		foreach ($this->getPools() as $pool)
		{
			if ($pool->hasTeam($team))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Return the Pool that has the fewest teams inside it
	 *
	 * @return Pool|null
	 */
	public function getSmallestPool()
	{
		$min = null;
		$smallestPool = null;
		foreach ($this->getPools() as $pool)
		{
			$nbTeams = count($pool->getTeams());
			if (is_null($min) || $nbTeams < $min)
			{
				$min = $nbTeams;
				$smallestPool = $pool;
			}
		}
		return $smallestPool;
	}

	/**
	 * Tell if the Phase is fully scheduled
	 *
	 * @return bool
	 */
	public function isFullyScheduled()
	{
		$isFullyScheduled = true;
		foreach ($this->getPools() as $pool)
		{
			$isFullyScheduled &= $pool->getScheduledRate() >= 1;
		}
		return $isFullyScheduled;
	}
}
