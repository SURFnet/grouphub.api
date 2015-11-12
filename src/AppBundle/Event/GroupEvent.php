<?php
/**
 * @file
 * Project: grouphub.api
 * File: UserAddEvent.php
 */

namespace AppBundle\Event;

use AppBundle\Entity\UserGroup;
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
}
