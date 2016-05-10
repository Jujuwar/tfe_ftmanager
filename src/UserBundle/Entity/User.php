<?php

namespace UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity(repositoryClass="\UserBundle\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser implements EquatableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="TeamBundle\Entity\Team", mappedBy="manager")
     */
    protected $team;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    public function isEqualTo(UserInterface $user)
    {
        if ($user instanceof User) {
            // Check that the roles are the same, in any order
            $isEqual = count($this->getRoles()) == count($user->getRoles());
            if ($isEqual) {
                foreach($this->getRoles() as $role) {
                    $isEqual = $isEqual && in_array($role, $user->getRoles());
                }
            }
            
            return $isEqual;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getTeam()
    {
        return $this->team;
    }
}

