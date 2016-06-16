<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class UserGroup
 *
 * @ORM\Entity
 * @ORM\Table(name="UserGroup")
 * @ExclusionPolicy("all")
 * @UniqueEntity("reference")
 */
class UserGroup
{
    const TYPE_LDAP = 'ldap';
    const TYPE_FORMAL = 'formal';
    const TYPE_GROUPHUB = 'grouphub';

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(name="UserGroupId", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Required()
     * @Expose()
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="UserGroupName", type="string", nullable=true)
     * @Required()
     * @Expose()
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="UserGroupDescription", type="string", nullable=true)
     * @Required()
     * @Expose()
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="UserGroupType", type="string", nullable=true)
     * @Required()
     * @Expose()
     */
    protected $type;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="UserGroupTimestamp", type="datetime", nullable=true)
     * @Expose()
     */
    protected $timestamp;

    /**
     * @var int
     *
     * @ORM\Column(name="UserGroupActive", type="smallint", nullable=true, options = { "default" = 1 })
     * @Required()
     */
    protected $active;

    /**
     * @var string
     *
     * @ORM\Column(name="Reference", type="string", unique=true, nullable=true, options={"collation":"latin1_general_ci"})
     * @Required()
     * @Expose()
     */
    protected $reference;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", fetch="EAGER")
     * @ORM\JoinColumn(name="UserId", referencedColumnName="UserId")
     * @Required()
     * @Expose()
     */
    protected $owner;

    /**
     * @var UserGroup
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserGroup", fetch="EAGER")
     * @ORM\JoinColumn(name="ParentGroupId", referencedColumnName="UserGroupId", nullable=true)
     * @Expose()
     */
    protected $parent;

    /**
     * @var UserInGroup[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserInGroup", mappedBy="group", fetch="EXTRA_LAZY")
     */
    protected $users;

    /**
     * @var int
     */
    private $userCount;

    /**
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * @return UserGroup
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param UserGroup $parent
     */
    public function setParent(UserGroup $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $user
     */
    public function setOwner(User $user)
    {
        $this->owner = $user;
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

    /**
     * @VirtualProperty
     *
     * @return int
     */
    public function getUserCount()
    {
        if ($this->userCount !== null) {
            return $this->userCount;
        }

        $criteria = Criteria::create()->where(Criteria::expr()->neq('role', UserInGroup::ROLE_PROSPECT));

        $this->userCount = $this->users->matching($criteria)->count();

        return $this->userCount;
    }
}
