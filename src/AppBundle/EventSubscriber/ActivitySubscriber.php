<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\UserActivity;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\DBALException;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ActivitySubscriber
 */
abstract class ActivitySubscriber implements EventSubscriberInterface
{
    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param Registry $doctrine
     * @param Logger   $logger
     */
    public function __construct(Registry $doctrine, Logger $logger)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
    }

    /**
     * Write activity to database.
     *
     * @param UserActivity $activity
     */
    protected function saveActivity(UserActivity $activity)
    {
        try {
            $em = $this->doctrine->getManager();
            $em->persist($activity);
            $em->flush();
        } catch (DBALException $e) {
            $this->logger->error(
                'Unable to save user activity log to database with message ' . $e->getMessage()
            );
        }
    }
}
