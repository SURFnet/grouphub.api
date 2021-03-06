<?php

namespace AppBundle\Manager;

use AppBundle\Entity\UserGroup;
use AppBundle\Entity\UserGroupInGroup;
use AppBundle\Event\GroupEvent;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class GroupManager
 */
class GroupManager
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
     * @param int $id
     *
     * @return UserGroup
     */
    public function findGroup($id)
    {
        return $this->doctrine->getRepository('AppBundle:UserGroup')->findOneBy(['id' => $id, 'active' => 1]);
    }

    /**
     * @param string $query
     * @param string $type
     * @param string $sortColumn
     * @param string $sortDir
     * @param int    $offset
     * @param int    $limit
     * @param array  $ids
     *
     * @return UserGroup[]
     */
    public function findGroups(
        $query = null,
        $type = null,
        $sortColumn = 'reference',
        $sortDir = 'ASC',
        $offset = 0,
        $limit = 100,
        array $ids = null
    ) {
        /** @var QueryBuilder $qb */
        $qb = $this->doctrine->getRepository('AppBundle:UserGroup')->createQueryBuilder('g');

        if ($type === 'ldap') {
            $qb->andWhere('g.type = \'ldap\'');
        }

        if ($type === '!ldap') {
            $qb->andWhere('g.type != \'ldap\'');
        }

        if ($type === 'formal') {
            $qb->andWhere('g.type = \'formal\'');
        }

        if (!empty($query)) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('g.name', ':query'),
                    $qb->expr()->like('g.description', ':query')
                )
            );

            $qb->setParameter('query', '%' . $query . '%');
        }

        if (!empty($ids)) {
            $qb->andWhere($qb->expr()->in('g.id', ':groups'));
            $qb->setParameter('groups', $ids);

            $offset = 0;
            $limit = count($ids);
        }

        $query = $qb
            ->andWhere('g.active = 1')
            ->orderBy('g.' . $sortColumn, $sortDir)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query);

        $result = [
            'count' => $paginator->count(),
            'items' => $paginator->getIterator()->getArrayCopy(),
        ];

        return $result;
    }

    /**
     * @param int    $groupId
     * @param string $sortColumn
     * @param string $sortDir
     * @param int    $offset
     * @param int    $limit
     *
     * @return UserGroup[]
     */
    public function findGroupsLinkableToGroup(
        $groupId,
        $sortColumn = 'reference',
        $sortDir = 'ASC',
        $offset = 0,
        $limit = 100
    ) {
        /** @var QueryBuilder $qb */
        $qb = $this->doctrine->getRepository('AppBundle:UserGroup')->createQueryBuilder('g');

        // Exclude group itself
        $qb->setParameter('groupId', $groupId);
        $qb->where('g.id != :groupId');

        // Exclude groups that have their own subgroups
        $qb->leftJoin(UserGroupInGroup::class, 'has_child', Join::LEFT_JOIN, 'has_child.group = g.id');
        $qb->andWhere($qb->expr()->isNull('has_child.group'));

        // Exclude groups that are already linked to this group
        $qb->leftJoin(UserGroupInGroup::class, 'super_group', Join::LEFT_JOIN, 'super_group.groupInGroup = g.id AND super_group.group = :groupId');
        $qb->andWhere($qb->expr()->isNull('super_group.group'));

        $query = $qb
            ->andWhere('g.active = 1')
            ->andWhere($qb->expr()->in('g.type', [
                UserGroup::TYPE_FORMAL,
                UserGroup::TYPE_GROUPHUB
            ]))
            ->groupBy('g.id')
            ->orderBy('g.' . $sortColumn, $sortDir)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query);

        $result = [
            'count' => $paginator->count(),
            'items' => $paginator->getIterator()->getArrayCopy(),
        ];

        return $result;
    }

    /**
     * @param UserGroup $group
     */
    public function addGroup(UserGroup $group)
    {
        $group->setActive(1);
        $group->setTimestamp(new DateTime());

        $em = $this->doctrine->getManager();
        $em->persist($group);
        $em->flush();

        $this->dispatcher->dispatch('app.event.group.add', new GroupEvent($group));
    }

    /**
     * @param UserGroup $group
     */
    public function updateGroup(UserGroup $group)
    {
        $this->doctrine->getManager()->flush();

        $this->dispatcher->dispatch('app.event.group.update', new GroupEvent($group));
    }

    /**
     * @param UserGroup $group
     */
    public function deleteGroup(UserGroup $group)
    {
        $children = $this->doctrine->getRepository('AppBundle:UserGroup')->findBy(['parent' => $group->getId(), 'active' => 1]);
        if (!empty($children)) {
            throw new \InvalidArgumentException('Parent groups cannot be disabled');
        }

        $this->dispatcher->dispatch('app.event.group.delete', new GroupEvent($group));

        $em = $this->doctrine->getManager();

        if ($group->getType() !== UserGroup::TYPE_LDAP) {
            $group->setActive(0);
        } else {
            $em->remove($group);
        }

        $em->flush();
    }
}
