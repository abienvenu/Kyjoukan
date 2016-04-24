<?php

namespace Abienvenu\KyjoukanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
	 * @ORM\ManyToOne(targetEntity="Event")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $event;

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
}
