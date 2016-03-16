<?php

namespace AppBundle\Manager;

use AppBundle\Entity\UserInGroup;
use AppBundle\Event\GroupEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class MembershipManager
 */
class MembershipManager
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
     * @param Registry                 $doctrine
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(Registry $doctrine, EventDispatcherInterface $dispatcher)
    {
        $this->doctrine = $doctrine;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param $userId
     * @param $groupId
     *
     * @return UserInGroup
     */
    public function findMembership($userId, $groupId)
    {
        $repo = $this->doctrine->getRepository('AppBundle:UserInGroup');

        return $repo->findOneBy(['user' => $userId, 'group' => $groupId]);
    }

    /**
     * @param UserInGroup $userInGroup
     */
    public function deleteMembership(UserInGroup $userInGroup)
    {
        $manager = $this->doctrine->getManager();
        $manager->remove($userInGroup);
        $manager->flush();

        $event = new GroupEvent($userInGroup->getGroup());
        $event->setUser($userInGroup);

        $this->dispatcher->dispatch('app.event.group.userdelete', $event);
    }

    /**
     * @param UserInGroup $userInGroup
     */
    public function updateMembership(UserInGroup $userInGroup)
    {
        $this->doctrine->getManager()->flush();

        $event = new GroupEvent($userInGroup->getGroup());
        $event->setUser($userInGroup);

        $this->dispatcher->dispatch('app.event.group.userupdate', $event);
    }
}
