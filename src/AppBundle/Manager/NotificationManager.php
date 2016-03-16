<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Notification;
use AppBundle\Entity\UserInGroup;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class NotificationManager
 */
class NotificationManager
{
    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param Registry $doctrine
     * @param EventDispatcherInterface $dispatcher
     * @param MembershipManager $membershipManager
     */
    public function __construct(
        Registry $doctrine,
        EventDispatcherInterface $dispatcher,
        MembershipManager $membershipManager
    ) {
        $this->doctrine = $doctrine;
        $this->dispatcher = $dispatcher;

        $this->membershipManager = $membershipManager;
    }

    /**
     * @param Notification $notification
     * @param string       $response
     */
    public function processNotification(Notification $notification, $response)
    {
        switch ($notification->getType()) {
            case Notification::TYPE_PROSPECT:
                $this->processProspectNotification($notification, $response);
                break;
        }

        $em = $this->doctrine->getManager();
        $em->remove($notification);
        $em->flush();
    }

    /**
     * @param Notification $notification
     * @param string       $response
     */
    private function processProspectNotification(Notification $notification, $response)
    {
        $userInGroup = $this->membershipManager->findMembership(
            $notification->getFrom()->getId(),
            $notification->getGroup()->getId()
        );

        if ($response === 'confirm') {
            $userInGroup->setRole(UserInGroup::ROLE_MEMBER);
            $this->membershipManager->updateMembership($userInGroup);

            $notification = new Notification(
                $userInGroup->getUser(),
                $notification->getTo(),
                Notification::TYPE_CONFIRMED,
                '',
                $userInGroup->getGroup()
            );
        } else {
            $this->membershipManager->deleteMembership($userInGroup);

            $notification = new Notification(
                $userInGroup->getUser(),
                $notification->getTo(),
                Notification::TYPE_DENIED,
                '',
                $userInGroup->getGroup()
            );
        }

        $em = $this->doctrine->getManager();
        $em->persist($notification);
        $em->flush();
    }

    /**
     * @param Notification $notification
     */
    public function deleteNotification(Notification $notification)
    {
        $em = $this->doctrine->getManager();
        $em->remove($notification);
        $em->flush();
    }
}
