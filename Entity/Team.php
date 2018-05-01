<?php

namespace Abienvenu\KyjoukanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
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
	 * @ORM\ManyToOne(targetEntity="Event", inversedBy="teams")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $event;

	public function getId() : int
	{
		return $this->id;
	}

	public function setName(string $name) : Team
	{
		$this->name = $name;
		return $this;
	}

	public function getName() : ?string
	{
		return $this->name;
	}

    public function setEvent(Event $event) : Team
    {
        $this->event = $event;
        return $this;
    }

    public function getEvent() : Event
    {
        return $this->event;
    }
}
