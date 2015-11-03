<?php
namespace AppBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Required;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class UserInGroup
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="UserInGroup")
 * @ExclusionPolicy("all")
 */
class UserInGroup
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(
     *     name="UserId",
     *     type="integer",
     *     length=11,
     *     nullable=false
     * )
     * @Required()
     * @Expose()
     */
    protected $userId;

    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(
     *     name="UserGroupId",
     *     type="integer",
     *     length=11,
     *     nullable=false
     * )
     * @Required()
     * @Expose()
     */
    protected $groupId;

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
