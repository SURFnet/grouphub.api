<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as orm;


/**
 * Class UserInGroup
 * @package AppBundle\Entity
 * @orm\Entity
 * @orm\Table(name="UserInGroup")
 */
class UserInGroup
{
    /**
     * @var int
     * @orm\Id()
     * @orm\Column(name="UserId", type="integer", length=11, nullable=false)
     */
    protected $userId;

    /**
     * @var int
     * @orm\Id()
     * @orm\Column(name="UserGroupId", type="integer", length=11, nullable=false)
     *
     */
    protected $groupId;

    /**
     * @var int
     * @orm\Column(name="UserInGroupRole", type="string", length=128, nullable=true)
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
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
}
