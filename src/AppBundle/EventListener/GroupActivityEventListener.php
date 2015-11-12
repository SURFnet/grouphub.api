<?php
/**
 * @file
 * Project: grouphub.api
 * File: UserEventListener.php
 */

namespace AppBundle\EventListener;

use AppBundle\Event\GroupEvent;

/**
 * Class UserEventListener
 * @package AppBundle\EventListener
 */
class GroupActivityEventListener extends ActivityEventListener
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'app.event.group.add' => 'groupAdd',
            'app.event.group.delete' => 'groupDelete',
            'app.event.group.update' => 'groupUpdate',
        ];
    }

    public function groupAdd(GroupEvent $event)
    {
        $activity = $this->getActivity(
            $event,
            'App:Event:User:Add',
            'User added to the database'
        );
        $this->saveActivity($activity);
    }

    public function groupUpdate(GroupEvent $event)
    {
        $activity = $this->getActivity(
            $event,
            'App:Event:User:Update',
            'User with id ' . $event->getUser()->getId() .
            ' updated'
        );
        $this->saveActivity($activity);
    }

    public function groupDelete(GroupEvent $event)
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
