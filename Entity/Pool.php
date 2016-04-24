<?php

namespace Abienvenu\KyjoukanBundle\Entity;

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
     * @ORM\ManyToOne(targetEntity="Phase")
     * @ORM\JoinColumn(nullable=false)
     */
    private $phase;

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
}
