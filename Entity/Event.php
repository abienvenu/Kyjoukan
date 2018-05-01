<?php

namespace Abienvenu\KyjoukanBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="event")
 * @ORM\Entity()
 */
class Event
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
	 * @Gedmo\Slug(fields={"name"})
	 * @ORM\Column(length=128, unique=true)
	 */
	private $slug;

	/**
	 * @ORM\OneToMany(targetEntity="Phase", mappedBy="event", cascade={"persist"})
	 */
	private $phases;


	/**
	 * @ORM\OneToMany(targetEntity="Ground", mappedBy="event", cascade={"persist"})
	 */
	private $grounds;

	/**
	 * @ORM\OneToMany(targetEntity="Team", mappedBy="event", cascade={"persist"})
	 */
	private $teams;

	public function __construct()
	{
		$this->phases = new ArrayCollection();
		$this->grounds = new ArrayCollection();
		$this->teams = new ArrayCollection();
	}

	public function getId() : int
	{
		return $this->id;
	}

	public function setName(string $name) : Event
	{
		$this->name = $name;
		return $this;
	}

	public function getName() : ?string
	{
		return $this->name;
	}

	public function setSlug(string $slug) : Event
	{
		$this->slug = $slug;
		return $this;
	}

	public function getSlug() : string
	{
		return $this->slug;
	}

	public function addPhase(Phase $phase) : Event
	{
		$this->phases[] = $phase;
		$phase->setEvent($this);
		return $this;
	}

	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getPhases()
	{
		return $this->phases;
	}

	public function addGround(Ground $ground) : Event
	{
		$this->grounds[] = $ground;
		$ground->setEvent($this);
		return $this;
	}

	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getGrounds()
	{
		return $this->grounds;
	}

    public function addTeam(Team $team) : Event
    {
        $this->teams[] = $team;
	    $team->setEvent($this);

        return $this;
    }

    public function removeTeam(Team $team) : void
    {
        $this->teams->removeElement($team);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeams()
    {
        return $this->teams;
    }
}
