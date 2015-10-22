<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as orm;

/**
 * Class UserActivity
 * @package AppBundle\Entity
 * @orm\Entity
 * @orm\Table(name="UserActivity")
 */
class UserActivity
{
    /**
     * @var int
     * @orm\Id()
     * @orm\Column(name="UserActivityId", type="integer", length=11)
     * @orm\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @orm\Column(name="UserActivityTimestamp", type="datetime", nullable=true)
     */
    protected $timestamp;

    /**
     * @var string
     * @orm\Column(name="UserActivityTitle", type="string", length=256, nullable=true)
     */
    protected $title;

    /**
     * @var string
     * @orm\Column(name="UserActivityDescription", type="string", length=256, nullable=true)
     */
    protected $description;

    /**
     * @var int
     * @orm\Column(name="UserActivityPriority", type="smallint", length=6)
     */
    protected $priority;

    /**
     * @var string
     * @orm\Column(name="UserActivityUrl", type="string", length=1024, nullable=true)
     */
    protected $url;

    /**
     * @var int
     * @orm\Column(name="UserId", type="integer", length=11)
     */
    protected $userId;

    /**
     * @var int
     * @orm\Column(name="UserGroupId", type="integer", length=11)
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
     * @param int $timestamp
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
