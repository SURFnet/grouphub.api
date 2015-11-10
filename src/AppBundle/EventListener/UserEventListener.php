<?php
/**
 * @file
 * Project: grouphub.api
 * File: UserEventListener.php
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\UserActivity;
use AppBundle\Event\UserEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Class UserEventListener
 * @package AppBundle\EventListener
 */
class UserEventListener implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * UserEventListener constructor.
     *
     * @param ContainerInterface $container
     */

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return Registry
     */
    protected function getDoctrine()
    {
        /** @var Registry $doctrine */
        $doctrine = $this->container->get('doctrine');
        return $doctrine;
    }

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
        $activity = $this->getActivity($event);
        $activity->setTitle('App:Event:User:Add');
        $activity->setDescription('User added to the database');

        $em = $this->getDoctrine()->getManager();
        $em->persist($activity);
        //$em->flush();
    }

    public function userUpdate(UserEvent $event)
    {
    }

    public function userDelete(UserEvent $event)
    {
    }

    /**
     * Get the user activity object
     * @param UserEvent $event
     * @return UserActivity
     */
    private function getActivity(UserEvent $event)
    {
        $activity = new UserActivity();
        $activity->setTimestamp(new \DateTime());
        $activity->setPriority(1);
        $activity->setUserId($event->getUser()->getId());

        return $activity;
    }
}
