<?php
/**
 * @file
 * Project: grouphub.api
 * File: UserAddEvent.php
 */

namespace AppBundle\Event;

use AppBundle\Entity\UserGroup;
use AppBundle\Entity\UserInGroup;
use AppBundle\Entity\UserGroupInGroup;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GroupEvent
 * @package AppBundle\Event
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
}
