<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class UserActivity
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="UserActivity")
 * @ExclusionPolicy("all")
 */
class UserActivity
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(name="UserActivityId", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose()
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="UserActivityTimestamp", type="datetime", nullable=false)
     * @Expose()
     */
    protected $timestamp;

    /**
     * @var string
     * @ORM\Column(name="UserActivityTitle", type="string", length=256, nullable=true)
     * @Expose()
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(name="UserActivityDescription", type="string", length=256, nullable=true)
     * @Expose()
     */
    protected $description;

    /**
     * @var int
     * @ORM\Column(name="UserActivityPriority", type="smallint", length=6)
     * @Expose()
     */
    protected $priority;

    /**
     * @var string
     * @ORM\Column(name="UserActivityUrl", type="string", length=1024, nullable=true)
     * @Expose()
     */
    protected $url;

    /**
     * @var int
     * @ORM\Column(name="UserId", type="integer", length=11, nullable=true)
     * @Expose()
     */
    protected $userId;

    /**
     * @var int
     * @ORM\Column(name="UserGroupId", type="integer", length=11, nullable=true)
     * @Expose()
     */
    protected $userGroupId;

    /**
     * @return int
     */
    public function getUserGroupId()
    {
        return $this->userGroupId;
    }

    /**
     * @param int $userGroupId
     */
    public function setUserGroupId($userGroupId)
    {
        $this->userGroupId = $userGroupId;
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