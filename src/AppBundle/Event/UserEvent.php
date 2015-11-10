<?php
/**
 * @file
 * Project: grouphub.api
 * File: UserAddEvent.php
 */


namespace AppBundle\Event;

use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class UserAddEvent
 * @package AppBundle\Event
 */
class UserEvent extends Event
{

    /**
     * @var User
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Return the events' user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
