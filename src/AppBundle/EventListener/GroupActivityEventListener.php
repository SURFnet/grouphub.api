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
            'app.event.group.add'         => 'groupAdd',
            'app.event.group.delete'      => 'groupDelete',
            'app.event.group.update'      => 'groupUpdate',
            'app.event.group.useradd'     => 'groupUserAdd',
            'app.event.group.userupdate'  => 'groupUserUpdate',
            'app.event.group.userdelete'  => 'groupUserDelete',
            'app.event.group.groupadd'    => 'groupGroupAdd',
            'app.event.group.groupdelete' => 'groupGroupDelete',
        ];
    }

    public function groupAdd(GroupEvent $event)
    {
        $name = $event->getGroup()->getName();
        $activity = $this->getActivity(
            $event,
            'App:Event:Group:Add',
            'Group ' . $name . ' added to the database'
        );
        $this->saveActivity($activity);
    }

    public function groupUpdate(GroupEvent $event)
    {
        $activity = $this->getActivity(
            $event,
            'App:Event:Group:Update',
            'Group with id ' . $event->getGroup()->getId() .
            ' updated'
        );
        $this->saveActivity($activity);
    }

    public function groupDelete(GroupEvent $event)
    {
        $activity = $this->getActivity(
            $event,
            'App:Event:Group:Delete',
            'Group with id ' . $event->getGroup()->getId() .
            ' deleted from the database.'
        );
        $this->saveActivity($activity);
    }

    public function groupUserAdd(GroupEvent $event)
    {
        $activity = $this->getActivity(
            $event,
            'App:Event:Group:userAdd',
            'Added user with id ' . $event->getUser()->getUserId() .
            ' to group with id ' . $event->getGroup()->getId()
        );
        $this->saveActivity($activity);
    }

    public function groupUserUpdate(GroupEvent $event)
    {
        $activity = $this->getActivity(
            $event,
            'App:Event:Group:userUpdate',
            'Updated user with id ' . $event->getUser()->getUserId() .
            ' to group with id ' . $event->getGroup()->getId()
        );
        $this->saveActivity($activity);
    }

    public function groupUserDelete(GroupEvent $event)
    {
        $activity = $this->getActivity(
            $event,
            'App:Event:Group:userDelete',
            'Removed user with id ' . $event->getUser()->getUserId() .
            ' from group with id ' . $event->getGroup()->getId()
        );
        $this->saveActivity($activity);
    }

    public function groupGroupDelete(GroupEvent $event)
    {
        $activity = $this->getActivity(
            $event,
            'App:Event:Group:groupDelete',
            'Removed group with id ' . $event->getGroupInGroup()->getGroupInGroupId() .
            ' from group with id ' . $event->getGroup()->getId()
        );
        $this->saveActivity($activity);
    }

    public function groupGroupAdd(GroupEvent $event)
    {
        $activity = $this->getActivity(
            $event,
            'App:Event:Group:groupAdd',
            'Added group with id ' . $event->getGroupInGroup()->getGroupInGroupId() .
            ' from to with id ' . $event->getGroup()->getId()
        );
        $this->saveActivity($activity);
    }
}
