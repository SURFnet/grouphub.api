<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as orm;

/**
 * Class UserGroupInGroup
 * @package AppBundle\Entity
 * @orm\Entity
 * @orm\Table(name="UserGroupInGroup")
 */
class UserGroupInGroup
{
    /**
     * @var int
     * @orm\Id()
     * @orm\Column(name="UserGroupInGroupId", type="integer", length=11, nullable=false)
     */
    protected $id;

    /**
     * @var int
     * @orm\Id()
     * @orm\Column(name="UserGroupId", type="integer", length=11, nullable=false)
     */
    protected $userGroupId;

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return UserGroupInGroup
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set userGroupId
     *
     * @param integer $userGroupId
     *
     * @return UserGroupInGroup
     */
    public function setUserGroupId($userGroupId)
    {
        $this->userGroupId = $userGroupId;

        return $this;
    }

    /**
     * Get userGroupId
     *
     * @return integer
     */
    public function getUserGroupId()
    {
        return $this->userGroupId;
    }
}
