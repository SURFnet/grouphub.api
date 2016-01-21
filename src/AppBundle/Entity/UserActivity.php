<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class UserActivity

 * @ORM\Entity
 * @ORM\Table(name="UserActivity")
 * @ExclusionPolicy("all")
 */
class UserActivity
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(name="UserActivityId", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose()
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="UserActivityTimestamp", type="datetime")
     * @Expose()
     */
    protected $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="UserActivityTitle", type="string", nullable=true)
     * @Expose()
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="UserActivityDescription", type="string", nullable=true)
     * @Expose()
     */
    protected $description;

    /**
     * @var int
     *
     * @ORM\Column(name="UserActivityPriority", type="smallint")
     * @Expose()
     */
    protected $priority;

    /**
     * @var string
     *
     * @ORM\Column(name="UserActivityUrl", type="string", nullable=true)
     * @Expose()
     */
    protected $url;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="UserId", referencedColumnName="UserId", nullable=true, onDelete="SET NULL")
     */
    protected $user;

    /**
     * @var UserGroup
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserGroup")
     * @ORM\JoinColumn(name="UserGroupId", referencedColumnName="UserGroupId", nullable=true, onDelete="SET NULL")
     */
    protected $userGroup;

    /**
     * @return UserGroup
     */
    public function getUserGroup()
    {
        return $this->userGroup;
    }

    /**
     * @param UserGroup $userGroup
     */
    public function setUserGroup(UserGroup $userGroup = null)
    {
        $this->userGroup = $userGroup;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
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
