<?php

namespace AppBundle\Manager;

use AppBundle\Entity\UserGroupInGroup;
use AppBundle\Event\GroupEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class GroupInGroupManager
 */
class GroupInGroupManager
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
     * @param int $groupId
     * @param int $groupInGroupId
     *
     * @return UserGroupInGroup
     */
    public function findGroupInGroup($groupId, $groupInGroupId)
    {
        return $this->doctrine->getRepository('AppBundle:UserGroupInGroup')->findOneBy(
            ['group' => $groupId, 'groupInGroup' => $groupInGroupId]
        );
    }

    /**
     * @param int $groupId
     *
     * @return UserGroupInGroup[]
     */
    public function findGroupInGroups($groupId)
    {
        return $this->doctrine->getRepository('AppBundle:UserGroupInGroup')->findBy(['group' => $groupId]);
    }

    /**
     * @param UserGroupInGroup $groupInGroup
     */
    public function addGroupInGroup(UserGroupInGroup $groupInGroup)
    {
        $em = $this->doctrine->getManager();
        $em->persist($groupInGroup);
        $em->flush();

        $event = new GroupEvent($groupInGroup->getGroup());
        $event->setGroupInGroup($groupInGroup);
        $this->dispatcher->dispatch('app.event.group.groupadd', $event);
    }

    /**
     * @param UserGroupInGroup $groupInGroup
     */
    public function deleteGroupInGroup(UserGroupInGroup $groupInGroup)
    {
        $em = $this->doctrine->getManager();
        $em->remove($groupInGroup);
        $em->flush();

        $event = new GroupEvent($groupInGroup->getGroup());
        $event->setGroupInGroup($groupInGroup);
        $this->dispatcher->dispatch('app.event.group.groupdelete', $event);
    }
}
