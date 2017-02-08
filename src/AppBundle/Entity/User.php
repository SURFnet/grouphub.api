<?php

namespace AppBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="User")
 * @Serializer\ExclusionPolicy("all")
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
     * @Serializer\Expose()
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
     * @Serializer\Expose()
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="UserLastName", nullable=true)
     * @Serializer\Expose()
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="DisplayName", nullable=true)
     * @Serializer\Expose()
     */
    protected $displayName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="UserLoginName", nullable=false)
     * @Serializer\Expose()
     * @Required()
     */
    protected $loginName;

    /**
     * @var string
     *
     * @Assert\Email()
     * @ORM\Column(type="string", name="EmailAddress", nullable=true)
     * @Serializer\Expose()
     */
    protected $emailAddress;

    /**
     * @var string
     *
     * @Assert\Url()
     * @ORM\Column(type="string", name="AvatarUrl", nullable=true)
     * @Serializer\Expose()
     */
    protected $avatarUrl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="UserTimestamp", nullable=true)
     * @Serializer\Expose()
     */
    protected $timeStamp;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="Reference", unique=true, options={"collation":"utf8_unicode_ci"})
     *
     * @Serializer\Expose()
     */
    protected $reference;

    /**
     * @var UserAnnotation[]
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\UserAnnotation",
     *     mappedBy="user",
     *     orphanRemoval=true,
     *     cascade={"persist"},
     *     fetch="EAGER"
     * )
     *
     * @Serializer\Expose()
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param int $id
     *
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
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
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
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
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     *
     * @return void
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @return string
     */
    public function getLoginName()
    {
        return $this->loginName;
    }

    /**
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
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param string $emailAddress
     *
     * @return void
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return string
     */
    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }

    /**
     * @param string $avatarUrl
     *
     * @return void
     */
    public function setAvatarUrl($avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;
    }

    /**
     * @return \DateTime
     */
    public function getTimeStamp()
    {
        return $this->timeStamp;
    }

    /**
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
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
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
