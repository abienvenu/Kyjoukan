<?php

namespace Abienvenu\KyjoukanBundle\Entity;

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
	 * @ORM\OneToMany(targetEntity="Game", mappedBy="pool")
	 */
	private $games;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->games = new ArrayCollection();
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGames()
    {
        return $this->games;
    }
}
