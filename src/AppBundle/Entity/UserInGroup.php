<?php

namespace AppBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class UserInGroup
 *
 * @ORM\Entity
 * @ORM\Table(name="UserInGroup")
 * @ExclusionPolicy("all")
 */
class UserInGroup
{
    /**
     * @var User
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="UserId", referencedColumnName="UserId", onDelete="CASCADE")
     * @Required()
     * @Expose()
     */
    protected $user;

    /**
     * @var UserGroup
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserGroup", inversedBy="users")
     * @ORM\JoinColumn(name="UserGroupId", referencedColumnName="UserGroupId", onDelete="CASCADE")
     * @Required()
     * @Expose()
     */
    protected $group;

    /**
     * @var string
     *
     * @ORM\Column(name="UserInGroupRole", type="string", nullable=true)
     * @Required()
     * @Expose()
     */
    protected $role;

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return UserGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param UserGroup $group
     */
    public function setGroup(UserGroup $group)
    {
        $this->group = $group;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }
}
