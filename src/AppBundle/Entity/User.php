<?php

namespace AppBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="User")
 * @ExclusionPolicy("all")
 * @UniqueEntity("reference")
 */
class User
{
    const REFERENCE_TRASH = 'sys:trash_user';

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", name="UserId")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose()
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $type = 'ldap';

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="UserFirstName", nullable=true)
     * @Expose()
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="UserLastName", nullable=true)
     * @Expose()
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="UserLoginName", nullable=false)
     * @Expose()
     * @Required()
     */
    protected $loginName;

    /**
     * @var string
     *
     * @ORM\Column(type="datetime", name="UserTimestamp", nullable=true)
     * @Expose()
     */
    protected $timeStamp;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="Reference", unique=true, options={"collation":"latin1_general_cs"})
     *
     * @Expose()
     */
    protected $reference;

    /**
     * @var UserAnnotation[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserAnnotation", mappedBy="user", orphanRemoval=true, cascade={"persist"}, fetch="EAGER")
     *
     * @Expose()
     */
    protected $annotations;

    /**
     *
     */
    public function __construct()
    {
        $this->annotations = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return User
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set loginName
     *
     * @param string $loginName
     *
     * @return User
     */
    public function setLoginName($loginName)
    {
        $this->loginName = $loginName;

        return $this;
    }

    /**
     * Get loginName
     *
     * @return string
     */
    public function getLoginName()
    {
        return $this->loginName;
    }

    /**
     * Set timeStamp
     *
     * @param \DateTime $timeStamp
     *
     * @return User
     */
    public function setTimeStamp($timeStamp)
    {
        $this->timeStamp = $timeStamp;

        return $this;
    }

    /**
     * Get timeStamp
     *
     * @return \DateTime
     */
    public function getTimeStamp()
    {
        return $this->timeStamp;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return User
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
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
     * @return UserAnnotation[]
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * @param UserAnnotation $annotation
     */
    public function addAnnotation(UserAnnotation $annotation)
    {
        $annotation->setUser($this);

        $this->annotations->add($annotation);
    }

    /**
     * @param UserAnnotation $annotation
     */
    public function removeAnnotation(UserAnnotation $annotation)
    {
        $this->annotations->removeElement($annotation);
    }
}
