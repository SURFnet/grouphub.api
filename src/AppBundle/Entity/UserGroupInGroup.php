<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class UserGroupInGroup
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="UserGroupInGroup"
 * )
 *
 * @ExclusionPolicy("all")
 */
class UserGroupInGroup
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(
     *     name="UserGroupInGroupId",
     *     type="integer",
     *     length=11,
     *     nullable=false
     * )
     * @Expose()
     */
    protected $groupInGroupId;

    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(
     *     name="UserGroupId",
     *     type="integer",
     *     length=11,
     *     nullable=false
     * )
     *
     * @Expose()
     */
    protected $groupId;

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return UserGroupInGroup
     */
    public function setGroupId($id)
    {
        $this->groupId = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set userGroupId
     *
     * @param integer $userGroupId
     *
     * @return UserGroupInGroup
     */
    public function setGroupInGroupId($userGroupId)
    {
        $this->groupInGroupId = $userGroupId;
        return $this;
    }

    /**
     * Get userGroupId
     *
     * @return integer
     */
    public function getGroupInGroupId()
    {
        return $this->groupInGroupId;
    }
}
