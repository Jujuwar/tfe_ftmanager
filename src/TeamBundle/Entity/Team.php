<?php

namespace TeamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="TeamBundle\Repository\TeamRepository")
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Le nom de l'équipe ne peut pas être vide")
     * @Assert\Length(max=50, maxMessage="Le nom de l'équipe ne peut pas excéder 50 caractères")
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registrationDate", type="datetime")
     */
    private $registrationDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="valid", type="boolean", options={"default":false})
     */
    private $valid;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\User", inversedBy="team")
     * @ORM\JoinColumn(name="manager", referencedColumnName="id")
     */
    private $manager;

    /**
     * @var bool
     *
     * @ORM\Column(name="registered", type="boolean", options={"default":false})
     */
    private $registered;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="TeamBundle\Entity\Player", mappedBy="team")
     */
    private $players;


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
     * Set registrationDate
     *
     * @param \DateTime $registrationDate
     * @return Team
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    /**
     * Get registrationDate
     *
     * @return \DateTime 
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Set valid
     *
     * @param boolean $valid
     * @return Team
     */
    public function setValid($valid)
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Get valid
     *
     * @return boolean 
     */
    public function getValid()
    {
        return $this->valid;
    }

    /**
     * Set manager
     *
     * @param \UserBundle\Entity\User $manager
     * @return Team
     */
    public function setManager(\UserBundle\Entity\User $manager = null)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager
     *
     * @return \UserBundle\Entity\User 
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set registered
     *
     * @param boolean $registered
     * @return Team
     */
    public function setRegistered($registered)
    {
        $this->registered = $registered;

        return $this;
    }

    /**
     * Get registered
     *
     * @return boolean 
     */
    public function getRegistered()
    {
        return $this->registered;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->players = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add players
     *
     * @param \TeamBundle\Entity\Player $players
     * @return Team
     */
    public function addPlayer(\TeamBundle\Entity\Player $players)
    {
        $this->players[] = $players;

        return $this;
    }

    /**
     * Remove players
     *
     * @param \TeamBundle\Entity\Player $players
     */
    public function removePlayer(\TeamBundle\Entity\Player $players)
    {
        $this->players->removeElement($players);
    }

    /**
     * Get players
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPlayers()
    {
        return $this->players;
    }
}
