<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as orm;


/**
 * @orm\Entity
 * @orm\Table(name="User")
 */
class User
{
    /**
     * @var int
     * @orm\Id()
     * @orm\Column(type="integer", length=11, name="UserId")
     * @orm\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @orm\Column(type="string", length=256, name="UserFirstName", nullable=true)
     */
    protected $firstName;

    /**
     * @var string
     * @orm\Column(type="string", length=256, name="UserLastName", nullable=true)
     */
    protected $lastName;

    /**
     * @var string
     * @orm\Column(type="string", length=256, name="UserLoginName", nullable=true)
     */
    protected $loginName;

    /**
     * @var string
     * @orm\Column(type="datetime", name="UserTimestamp", nullable=true)
     */
    protected $timeStamp;

    /**
     * @var string
     * @orm\Column(type="string", length=256, name="Reference")
     */
    protected $reference;

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
}
