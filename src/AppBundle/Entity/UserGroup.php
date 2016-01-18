<?php
namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class UserGroup
 *
 * @ORM\Entity
 * @ORM\Table(name="UserGroup")
 * @ExclusionPolicy("all")
 */
class UserGroup
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(name="UserGroupId", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Required()
     * @Expose()
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="UserGroupName", type="string", length=256, nullable=true)
     * @Required()
     * @Expose()
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="UserGroupDescription", type="string", length=4096, nullable=true)
     * @Required()
     * @Expose()
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(name="UserGroupType", type="string", length=128, nullable=true)
     * @Required()
     * @Expose()
     */
    protected $type;

    /**
     * @var DateTime
     * @ORM\Column(name="UserGroupTimestamp", type="datetime", nullable=true)
     * @Expose()
     */
    protected $timestamp;

    /**
     * @var int
     * @ORM\Column(
     *  name="UserGroupActive",
     *  type="smallint",
     *  length=6,
     *  nullable=true,
     *  options = { "default" = 1 }
     * )
     * @Required()
     * @Expose()
     */
    protected $active;

    /**
     * @var string
     * @ORM\Column(name="Reference", type="string", length=128, unique=true)
     * @Required()
     * @Expose()
     */
    protected $reference;

    /**
     * @var int
     * @ORM\Column(name="UserId", type="integer", length=11)
     * @Required()
     * @Expose()
     */
    protected $ownerId;

    /**
     * @var int
     * @ORM\Column(
     *  name="ParentGroupId",
     *  type="integer",
     *  length=11,
     *  options={ "default" = 0 }
     * )
     * @Required()
     * @Expose()
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
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     * @param int $userId
     */
    public function setOwnerId($userId)
    {
        $this->ownerId = $userId;
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
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param DateTime $timestamp
     */
    public function setTimestamp(DateTime $timestamp)
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
