<?php
/**
 * @file
 * Project: grouphub.api
 * File: UserActivityEventListener.php
 */

namespace AppBundle\EventListener;

use AppBundle\Event\UserEvent;
use AppBundle\Entity\UserActivity;

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

    /**
     * Get the user activity object
     *
     * @param UserEvent $event
     * @param string $title
     * @param string|null $description
     * @return UserActivity
     */
    protected function getActivity(UserEvent $event, $title, $description = null)
    {
        $activity = new UserActivity();
        $activity->setTimestamp(new \DateTime());
        $activity->setPriority(1);
        $activity->setUserId($event->getUser()->getId());
        $activity->setTitle($title);
        $activity->setDescription($description);

        return $activity;
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
