<?php
/**
 * @file
 * Project: grouphub.api
 * File: EventListener.php
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\UserActivity;
use Doctrine\DBAL\DBALException;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Class EventListener
 * @package AppBundle\EventListener
 */
abstract class ActivityEventListener implements EventSubscriberInterface
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
     * Get the user activity object
     *
     * @param Event $event
     * @param string $title
     * @param string|null $description
     * @return UserActivity
     */
    protected function getActivity(Event $event, $title, $description = null)
    {
        $activity = new UserActivity();
        $activity->setTimestamp(new \DateTime());
        $activity->setPriority(1);
        $activity->setUserId(NULL);
        $activity->setUserGroupId($event->getGroup()->getId());
        $activity->setTitle($title);
        $activity->setDescription($description);

        return $activity;
    }

    /**
     * Write activity to database.
     *
     * @param UserActivity $activity
     */
    protected function saveActivity(UserActivity $activity)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($activity);
            $em->flush();
        }
        catch (DBALException $e) {
            $this->container->get('logger')->error(
                'Unable to save user activity log to database with message ' .
                $e->getMessage()
            );
        }
    }
}
