<?php

namespace Abienvenu\KyjoukanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="Abienvenu\KyjoukanBundle\Repository\TeamRepository")
 */
class Team
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
	 * @return Team
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
     * Set event
     *
     * @param Event $event
     * @return Team
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
