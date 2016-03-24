<?php

namespace AppBundle\Manager;

use AppBundle\Entity\UserGroup;
use AppBundle\Event\GroupEvent;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Registry;
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

        $group->setActive(0);

        $this->doctrine->getManager()->flush();

        $this->dispatcher->dispatch('app.event.group.delete', new GroupEvent($group));
    }
}
