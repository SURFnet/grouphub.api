<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class UserGroupInGroup
 *
 * @ORM\Entity
 * @ORM\Table(name="UserGroupInGroup")
 * @ExclusionPolicy("all")
 */
class UserGroupInGroup
{
    /**
     * @var UserGroup
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserGroup")
     * @ORM\JoinColumn(name="UserGroupInGroupId", referencedColumnName="UserGroupId")
     * @Expose()
     */
    protected $groupInGroup;

    /**
     * @var UserGroup
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserGroup")
     * @ORM\JoinColumn(name="UserGroupId", referencedColumnName="UserGroupId")
     */
    protected $group;

    /**
     * @param UserGroup $group
     */
    public function setGroup(UserGroup $group)
    {
        $this->group = $group;
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
    public function setGroupInGroup(UserGroup $group)
    {
        $this->groupInGroup = $group;
    }

    /**
     * @return UserGroup
     */
    public function getGroupInGroup()
    {
        return $this->groupInGroup;
    }
}
