<?php

namespace AppBundle\Event;

use AppBundle\Entity\UserGroup;
use AppBundle\Entity\UserGroupInGroup;
use AppBundle\Entity\UserInGroup;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GroupEvent
 */
class GroupEvent extends Event
{
    /**
     * @var UserGroup
     */
    protected $group;

    /**
     * @var UserInGroup
     */
    protected $user;

    /**
     * @var UserGroupInGroup
     */
    protected $groupInGroup;

    /**
     * @var string
     */
    protected $message;

    /**
     * @param UserGroup $group
     */
    public function __construct(UserGroup $group)
    {
        $this->group = $group;
    }

    /**
     * Return the events' group
     *
     * @return UserGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param UserInGroup $user
     */
    public function setUser(UserInGroup $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInGroup
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return UserGroupInGroup
     */
    public function getGroupInGroup()
    {
        return $this->groupInGroup;
    }

    /**
     * @param UserGroupInGroup $groupInGroup
     */
    public function setGroupInGroup($groupInGroup)
    {
        $this->groupInGroup = $groupInGroup;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}
