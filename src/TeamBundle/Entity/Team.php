<?php

namespace TeamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

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
     * @ORM\Column(name="valid", type="boolean")
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
}
