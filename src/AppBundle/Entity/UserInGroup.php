<?php

namespace AppBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Required;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\JoinColumn(name="UserId", referencedColumnName="UserId"))
     * @Required()
     * @Expose()
     */
    protected $user;

    /**
     * @var UserGroup
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserGroup")
     * @ORM\JoinColumn(name="UserGroupId", referencedColumnName="UserGroupId")
     * @Required()
     * @Expose()
     */
    protected $group;

    /**
     * @var string
     * @ORM\Column(
     *     name="UserInGroupRole",
     *     type="string",
     *     length=128,
     *     nullable=true
     * )
     * @Required()
     * @Expose()
     */
    protected $role;

    /**
     * @return int
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param int $role
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
