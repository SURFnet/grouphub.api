<?php

namespace AppBundle\Manager;

use AppBundle\Entity\User;
use AppBundle\Entity\UserInGroup;
use AppBundle\Event\GroupEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
     * @param int    $groupId
     * @param string $query
     * @param string $role
     * @param array  $users
     * @param string $sort
     * @param int    $offset
     * @param int    $limit
     *
     * @return UserInGroup[]
     */
    public function findMemberships(
        $groupId,
        $query = null,
        $role = null,
        array $users = [],
        $sort = 'reference',
        $offset = 0,
        $limit = 100
    ) {
        /** @var QueryBuilder $qb */
        $qb = $this->doctrine->getRepository('AppBundle:UserInGroup')->createQueryBuilder('ug');

        if (!empty($query)) {
            $terms = explode(' ', $query);

            foreach ($terms as $i => $term) {
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like('u.firstName', ':term' . $i),
                        $qb->expr()->like('u.lastName', ':term' . $i),
                        $qb->expr()->like('u.loginName', ':term' . $i)
                    )
                );

                $qb->setParameter('term' . $i, '%' . $term . '%');
            }
        }

        if (!empty($users)) {
            $qb->andWhere($qb->expr()->in('ug.user', ':users'));
            $qb->setParameter('users', $users);

            $offset = 0;
            $limit = count($users);
        }

        if (!empty($role)) {
            $qb->andWhere('ug.role = :role')->setParameter('role', $role);
        }

        if ($sort === 'name') {
            $sort = new Expr\OrderBy('u.lastName');
            $sort->add('u.firstName');
        } else {
            $sort = new Expr\OrderBy('u.' . $sort);
        }

        $qb
            ->andWhere('ug.group = :id')
            ->setParameter('id', $groupId)
            ->join('ug.user', 'u')
            ->orderBy($sort)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $qb->getQuery();

        $paginator = new Paginator($query, false);

        $result = [
            'count' => $paginator->count(),
            'items' => $paginator->getIterator()->getArrayCopy(),
        ];

        return $result;
    }

    /**
     * @param UserInGroup $userInGroup
     * @param             $message
     */
    public function addMembership(UserInGroup $userInGroup, $message)
    {
        /** @var EntityManager $manager */
        $manager = $this->doctrine->getManager();
        $existingUser = $manager->getRepository(UserInGroup::class)->findBy([
            'user' => $userInGroup->getUser(),
            'group' => $userInGroup->getGroup()
        ]);

        if ($existingUser instanceof UserInGroup) {
            return;
        }

        $manager->persist($userInGroup);
        $manager->flush();

        $event = new GroupEvent($userInGroup->getGroup());
        $event->setUser($userInGroup);
        $event->setMessage($message);

        $this->dispatcher->dispatch('app.event.group.useradd', $event);
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
