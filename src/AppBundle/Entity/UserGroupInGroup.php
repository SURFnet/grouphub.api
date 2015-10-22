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
}