<?php

namespace Abienvenu\KyjoukanBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="phase")
 * @ORM\Entity()
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
	 * @ORM\OneToMany(targetEntity="Pool", mappedBy="phase", cascade={"persist", "remove"})
	 */
	private $pools;

	/**
	 * @ORM\OneToMany(targetEntity="Round", mappedBy="phase", cascade={"persist", "remove"})
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

	public function getId() : int
	{
		return $this->id;
	}

	public function setName(string $name) : Phase
	{
		$this->name = $name;
		return $this;
	}

	public function getName() : ?string
	{
		return $this->name;
	}

	public function setRule(int $rule) : Phase
	{
		$this->rule = $rule;
		return $this;
	}

	public function getRule() : ?int
	{
		return $this->rule;
	}

	public function setStartDateTime(\DateTime $startDateTime) : Phase
	{
		$this->startDateTime = $startDateTime;
		return $this;
	}

	public function getStartDateTime() : ?\DateTime
	{
		return $this->startDateTime;
	}

	public function setRoundDuration(int $roundDuration) : Phase
	{
		$this->roundDuration = $roundDuration;
		return $this;
	}

	public function getRoundDuration() : ?int
	{
		return $this->roundDuration;
	}

	public function setEvent(Event $event) : Phase
	{
		$this->event = $event;

		return $this;
	}

	public function getEvent() : Event
	{
		return $this->event;
	}

	public function addPool(Pool $pool) : Phase
	{
		$this->pools[] = $pool;
		$pool->setPhase($this);

		return $this;
	}

	public function removePool(Pool $pool)
	{
		$this->pools->removeElement($pool);
	}

	/**
	 * @return Pool[]
	 */
	public function getPools() : Collection
	{
		return $this->pools;
	}

	public function addRound(Round $round) : Phase
	{
		$this->rounds[] = $round;
		$round->setPhase($this);
		return $this;
	}

	public function removeRound(Round $round)
	{
		$this->rounds->removeElement($round);
	}

	/**
	 * @return Round[]
	 */
	public function getRounds() : Collection
	{
		return $this->rounds;
	}

	public function setSlug(string $slug) : Phase
	{
		$this->slug = $slug;
		return $this;
	}

	public function getSlug() : string
	{
		return $this->slug;
	}

	public function addTeam(Team $team) : Phase
	{
		$this->teams[] = $team;
		return $this;
	}

	public function removeTeam(Team $team)
	{
		$this->teams->removeElement($team);
	}

	/**
	 * @return Team[]
	 */
	public function getTeams() : Collection
	{
		return $this->teams;
	}

	/**
	 * Determine if the Team is already registered in the Phase
	 */
	public function hasTeam(Team $team) : bool
	{
		return $this->getTeams()->contains($team);
	}

	/**
	 * Determine if the Team is inside a Pool of the Phase
	 */
	public function isTeamPooled(Team $team) : bool
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
	 */
	public function getSmallestPool() : ?Pool
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
	 */
	public function isFullyScheduled() : bool
	{
		$isFullyScheduled = true;
		foreach ($this->getPools() as $pool)
		{
			$isFullyScheduled &= ($pool->getScheduledRate() >= 1 || count($pool->getTeams()) <= 1);
		}
		return $isFullyScheduled;
	}
}
