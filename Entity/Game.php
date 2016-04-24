<?php

namespace Abienvenu\KyjoukanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="Abienvenu\KyjoukanBundle\Repository\GameRepository")
 */
class Game
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
     * @ORM\Column(name="team1", type="string", length=255)
     */
    private $team1;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="team2", type="object")
     */
    private $team2;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="ground", type="object")
     */
    private $ground;

    /**
     * @var int
     *
     * @ORM\Column(name="score1", type="integer")
     */
    private $score1;

    /**
     * @var int
     *
     * @ORM\Column(name="score2", type="integer", nullable=true)
     */
    private $score2;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;


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
     * Set team1
     *
     * @param string $team1
     * @return Game
     */
    public function setTeam1($team1)
    {
        $this->team1 = $team1;

        return $this;
    }

    /**
     * Get team1
     *
     * @return string 
     */
    public function getTeam1()
    {
        return $this->team1;
    }

    /**
     * Set team2
     *
     * @param \stdClass $team2
     * @return Game
     */
    public function setTeam2($team2)
    {
        $this->team2 = $team2;

        return $this;
    }

    /**
     * Get team2
     *
     * @return \stdClass 
     */
    public function getTeam2()
    {
        return $this->team2;
    }

    /**
     * Set ground
     *
     * @param \stdClass $ground
     * @return Game
     */
    public function setGround($ground)
    {
        $this->ground = $ground;

        return $this;
    }

    /**
     * Get ground
     *
     * @return \stdClass 
     */
    public function getGround()
    {
        return $this->ground;
    }

    /**
     * Set score1
     *
     * @param integer $score1
     * @return Game
     */
    public function setScore1($score1)
    {
        $this->score1 = $score1;

        return $this;
    }

    /**
     * Get score1
     *
     * @return integer 
     */
    public function getScore1()
    {
        return $this->score1;
    }

    /**
     * Set score2
     *
     * @param integer $score2
     * @return Game
     */
    public function setScore2($score2)
    {
        $this->score2 = $score2;

        return $this;
    }

    /**
     * Get score2
     *
     * @return integer 
     */
    public function getScore2()
    {
        return $this->score2;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return Game
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
