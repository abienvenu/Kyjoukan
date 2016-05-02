<?php

namespace Abienvenu\KyjoukanBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="Abienvenu\KyjoukanBundle\Repository\EventRepository")
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

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->phases = new ArrayCollection();
		$this->grounds = new ArrayCollection();
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
	 * Set name
	 *
	 * @param string $name
	 * @return Event
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
	 * Set slug
	 *
	 * @param string $slug
	 * @return Event
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
	 * Add phases
	 *
	 * @param Phase $phase
	 * @return Event
	 */
	public function addPhase(Phase $phase)
	{
		$this->phases[] = $phase;
		$phase->setEvent($this);
		return $this;
	}

	/**
	 * Remove phases
	 *
	 * @param Phase $phases
	 */
	public function removePhase(Phase $phases)
	{
		$this->phases->removeElement($phases);
	}

	/**
	 * Get phases
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getPhases()
	{
		return $this->phases;
	}

	/**
	 * Add ground
	 *
	 * @param Ground $ground
	 * @return Event
	 */
	public function addGround(Ground $ground)
	{
		$this->grounds[] = $ground;
		$ground->setEvent($this);

		return $this;
	}

	/**
	 * Remove grounds
	 *
	 * @param Ground $ground
	 */
	public function removeGround(Ground $ground)
	{
		$this->grounds->removeElement($ground);
	}

	/**
	 * Get grounds
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getGrounds()
	{
		return $this->grounds;
	}

    /**
     * Add team
     *
     * @param Team $team
     * @return Event
     */
    public function addTeam(Team $team)
    {
        $this->teams[] = $team;
	    $team->setEvent($this);

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
}
