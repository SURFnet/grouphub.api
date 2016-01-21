<?php

namespace AppBundle\EventListener;

use AppBundle\Event\GroupEvent;
use AppBundle\Entity\UserActivity;

/**
 * Class UserEventListener
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

    /**
     * Get the user activity object
     *
     * @param GroupEvent $event
     * @param string $title
     * @param string|null $description
     * @return UserActivity
     */
    protected function getActivity(GroupEvent $event, $title, $description = null)
    {
        $activity = new UserActivity();
        $activity->setTimestamp(new \DateTime());
        $activity->setPriority(1);
        $activity->setUser($event->getUser() ? $event->getUser()->getUser() : null);
        $activity->setUserGroup($event->getGroup());
        $activity->setTitle($title);
        $activity->setDescription($description);

        return $activity;
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
            'Added user with id ' . $event->getUser()->getUser()->getId() .
            ' to group with id ' . $event->getGroup()->getId()
        );
        $this->saveActivity($activity);
    }

    public function groupUserUpdate(GroupEvent $event)
    {
        $activity = $this->getActivity(
            $event,
            'App:Event:Group:userUpdate',
            'Updated user with id ' . $event->getUser()->getUser()->getId() .
            ' to group with id ' . $event->getGroup()->getId()
        );
        $this->saveActivity($activity);
    }

    public function groupUserDelete(GroupEvent $event)
    {
        $activity = $this->getActivity(
            $event,
            'App:Event:Group:userDelete',
            'Removed user with id ' . $event->getUser()->getUser()->getId() .
            ' from group with id ' . $event->getGroup()->getId()
        );
        $this->saveActivity($activity);
    }

    public function groupGroupDelete(GroupEvent $event)
    {
        $activity = $this->getActivity(
            $event,
            'App:Event:Group:groupDelete',
            'Removed group with id ' . $event->getGroupInGroup()->getGroup()->getId() .
            ' from group with id ' . $event->getGroup()->getId()
        );
        $this->saveActivity($activity);
    }

    public function groupGroupAdd(GroupEvent $event)
    {
        $activity = $this->getActivity(
            $event,
            'App:Event:Group:groupAdd',
            'Added group with id ' . $event->getGroupInGroup()->getGroup()->getId() .
            ' from to with id ' . $event->getGroup()->getId()
        );
        $this->saveActivity($activity);
    }
}
