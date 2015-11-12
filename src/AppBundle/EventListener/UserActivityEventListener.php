<?php
/**
 * @file
 * Project: grouphub.api
 * File: UserActivityEventListener.php
 */

namespace AppBundle\EventListener;

use AppBundle\Event\UserEvent;

/**
 * Class UserEventListener
 * @package AppBundle\EventListener
 */
class UserActivityEventListener extends ActivityEventListener
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'app.event.user.add' => 'userAdd',
            'app.event.user.delete' => 'userDelete',
            'app.event.user.update' => 'userUpdate',
        ];
    }

    public function userAdd(UserEvent $event)
    {
        $activity = $this->getActivity(
            $event,
            'App:Event:User:Add',
            'User added to the database'
        );
        $this->saveActivity($activity);
    }

    public function userUpdate(UserEvent $event)
    {
        $activity = $this->getActivity(
            $event,
            'App:Event:User:Update',
            'User with id ' . $event->getUser()->getId() .
            ' updated'
        );
        $this->saveActivity($activity);
    }

    public function userDelete(UserEvent $event)
    {
        $activity = $this->getActivity(
            $event,
            'App:Event:User:Delete',
            'User with id ' . $event->getUser()->getId() .
            ' deleted from the database.'
        );
        $this->saveActivity($activity);
    }
}
