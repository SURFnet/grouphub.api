<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as orm;

/**
 * Class UserGroup
 * @package AppBundle\Entity
 * @orm\Entity
 * @orm\Table(name="UserGroup")
 */
class UserGroup
{
    /**
     * @var int
     * @orm\Id()
     * @orm\Column(name="UserGroupId", type="integer", length=11)
     * @orm\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @orm\Column(name="UserGroupName", type="string", length=256, nullable=true)
     */
    protected $name;

    /**
     * @var string
     * @orm\Column(name="UserGroupDescription", type="string", length=4096, nullable=true)
     */
    protected $description;

    /**
     * @var string
     * @orm\Column(name="UserGroupType", type="string", length=128, nullable=true)
     */
    protected $type;

    /**
     * @var int
     * @orm\Column(name="UserGroupTimestamp", type="datetime", nullable=true)
     */
    protected $timestamp;

    /**
     * @var int
     * @orm\Column(name="UserGroupActive", type="smallint", length=6, nullable=true)
     */
    protected $active;

    /**
     * @var string
     * @orm\Column(name="Reference", type="string", length=128)
     */
    protected $reference;

    /**
     * @var int
     * @orm\Column(name="UserId", type="integer", length=11)
     */
    protected $userId;

    /**
     * @var int
     * @orm\Column(name="ParentGroupId", type="integer", length=11)
     */
    protected $parent;

    /**
     * @return int
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param int $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
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

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param int $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
