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
     * @var int $id
     * @orm\Column(name="UserActivityId", type="integer", length=11)
     */
    protected $id;

    /**
     * @var string $timestamp
     * @orm\Column(name="UserActivityTimestamp", type="datetime")
     */
    protected $timestamp;

    /**
     * @var string $title
     * @orm\Column(name="UserActivityTitle", type="string", length=256)
     */
    protected $title;

    /**
     * @var string $description
     * @orm\Column(name="UserActivityDescription", type="string", length=256)
     */
    protected $description;

    /**
     * @var int $priority
     * @orm\Column(name="UserActivityPriority", type="smallint", length=6)
     */
    protected $priority;

    /**
     * @var string $url
     * @orm\Column(name="UserActivityUrl", type=string, length=1024)
     */
    protected $url;

    /**
     * @var int $userId
     * @orm\Column(name="UserId", type="integer", length=11)
     */
    protected $userId;

    /**
     * @var int $userGroupId
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
