<?php

namespace MatchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Prestation
 *
 * @ORM\Table(name="prestation")
 * @ORM\Entity(repositoryClass="MatchBundle\Repository\PrestationRepository")
 */
class Prestation
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
     * @var int
     *
     * @ORM\Column(name="buts", type="smallint")
     */
    private $buts;

    /**
     * @var int
     *
     * @ORM\Column(name="yellowCards", type="smallint")
     */
    private $yellowCards;

    /**
     * @var int
     *
     * @ORM\Column(name="redCards", type="smallint")
     */
    private $redCards;

    /**
     * @var int
     *
     * @ORM\Column(name="enterTime", type="smallint")
     */
    private $enterTime;

    /**
     * @var int
     *
     * @ORM\Column(name="leaveTime", type="smallint")
     */
    private $leaveTime;

    /**
     * @var \TeamBundle\Entity\Player
     *
     * @ORM\ManyToOne(targetEntity="TeamBundle\Entity\Player")
     * @ORM\JoinColumn(name="player", referencedColumnName="id")
     */
    private $player;

    /**
     * @var \MatchBundle\Entity\Matchs
     * 
     * @ORM\ManyToOne(targetEntity="MatchBundle\Entity\Matchs", inversedBy="prestations")
     * @ORM\JoinColumn(name="matchs", referencedColumnName="id")
     */
    private $matchs;

    /**
     * @var \TeamBundle\Entity\Team
     *
     * @ORM\ManyToOne(targetEntity="TeamBundle\Entity\Team")
     * @ORM\JoinColumn(name="team", referencedColumnName="id")
     */
    private $team;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set buts
     *
     * @param integer $buts
     *
     * @return Prestation
     */
    public function setButs($buts)
    {
        $this->buts = $buts;

        return $this;
    }

    /**
     * Get buts
     *
     * @return int
     */
    public function getButs()
    {
        return $this->buts;
    }

    /**
     * Set yellowCards
     *
     * @param integer $yellowCards
     *
     * @return Prestation
     */
    public function setYellowCards($yellowCards)
    {
        $this->yellowCards = $yellowCards;

        return $this;
    }

    /**
     * Get yellowCards
     *
     * @return int
     */
    public function getYellowCards()
    {
        return $this->yellowCards;
    }

    /**
     * Set redCards
     *
     * @param integer $redCards
     *
     * @return Prestation
     */
    public function setRedCards($redCards)
    {
        $this->redCards = $redCards;

        return $this;
    }

    /**
     * Get redCards
     *
     * @return int
     */
    public function getRedCards()
    {
        return $this->redCards;
    }

    /**
     * Set enterTime
     *
     * @param integer $enterTime
     *
     * @return Prestation
     */
    public function setEnterTime($enterTime)
    {
        $this->enterTime = $enterTime;

        return $this;
    }

    /**
     * Get enterTime
     *
     * @return int
     */
    public function getEnterTime()
    {
        return $this->enterTime;
    }

    /**
     * Set leaveTime
     *
     * @param integer $leaveTime
     *
     * @return Prestation
     */
    public function setLeaveTime($leaveTime)
    {
        $this->leaveTime = $leaveTime;

        return $this;
    }

    /**
     * Get leaveTime
     *
     * @return int
     */
    public function getLeaveTime()
    {
        return $this->leaveTime;
    }

    /**
     * Set player
     *
     * @param \TeamBundle\Entity\Player $player
     *
     * @return Prestation
     */
    public function setPlayer(\TeamBundle\Entity\Player $player = null)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return \TeamBundle\Entity\Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set matchs
     *
     * @param \MatchBundle\Entity\Matchs $matchs
     *
     * @return Prestation
     */
    public function setMatchs(\MatchBundle\Entity\Matchs $matchs = null)
    {
        $this->matchs = $matchs;

        return $this;
    }

    /**
     * Get matchs
     *
     * @return \MatchBundle\Entity\Matchs
     */
    public function getMatchs()
    {
        return $this->matchs;
    }

    /**
     * Set team
     *
     * @param \TeamBundle\Entity\Team $team
     *
     * @return Prestation
     */
    public function setTeam(\TeamBundle\Entity\Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \TeamBundle\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }
}
