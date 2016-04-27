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
	 * @ORM\OneToMany(targetEntity="Pool", mappedBy="phase")
	 */
	private $pools;

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
	 * Constructor
	 */
	public function __construct()
	{
		$this->pools = new ArrayCollection();
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
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getPools()
	{
		return $this->pools;
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
}
