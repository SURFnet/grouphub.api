<?php

namespace AppBundle\Manager;

use AppBundle\Entity\UserGroup;
use AppBundle\Entity\UserInGroup;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * UserGroupManager
 */
class UserGroupManager
{
    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param int    $userId
     * @param string $type
     * @param string $sortColumn
     * @param string $sortDir
     * @param int    $offset
     * @param int    $limit
     *
     * @return array
     */
    public function findUserGroups($userId, $type = null, $sortColumn = 'reference', $sortDir = 'ASC', $offset = 0, $limit = 10)
    {
        $result = [
            'count' => 0,
            'items' => [],
        ];

        foreach (['owner', UserInGroup::ROLE_ADMIN, UserInGroup::ROLE_MEMBER, UserInGroup::ROLE_PROSPECT] as $role) {
            $groups = $this->findUserGroupsForRole($userId, $role, $type, $sortColumn, $sortDir, max(0, $offset), $limit);

            $result['count'] += $groups['count'];
            $result['items'] = array_merge($result['items'], $groups['items']);

            $offset -= $groups['count'] - count($groups['items']);
            $limit -= count($groups['items']);
        }

        return $result;
    }

    /**
     * @param int    $userId
     * @param string $type
     * @param string $sortColumn
     * @param string $sortDir
     * @param int    $offset
     * @param int    $limit
     *
     * @return array of collections
     */
    public function findUserGroupsGroupedByTypeAndRole($userId, $type = null, $sortColumn = 'reference', $sortDir = 'ASC', $offset = 0, $limit = 10)
    {
        $result = [];

        if ($type === null) {
            $types = [UserGroup::TYPE_GROUPHUB, 'other'];
        } else {
            $types = [$type];
        }

        foreach ($types as $type) {
            foreach (['owner', UserInGroup::ROLE_ADMIN, UserInGroup::ROLE_MEMBER, UserInGroup::ROLE_PROSPECT] as $role) {
                $result[$type][$role] = $this->findUserGroupsForRole(
                    $userId,
                    $role,
                    $type,
                    $sortColumn,
                    $sortDir,
                    $offset,
                    $limit
                );
            }
        }

        return $result;
    }

    /**
     * @param int    $userId
     * @param string $role
     * @param string $type
     * @param string $sortColumn
     * @param string $sortDir
     * @param int    $offset
     * @param int    $limit
     *
     * @return array collection
     */
    public function findUserGroupsForRole($userId, $role, $type = null, $sortColumn = 'reference', $sortDir = 'ASC', $offset = 0, $limit = 10)
    {
        if ($role === 'owner') {
            $query = $this->getOwnerGroupsQuery($userId, $type, $sortColumn, $sortDir, $offset, $limit);

            $paginator = new Paginator($query);

            $result = [
                'count' => $paginator->count(),
                'items' => [],
            ];

            foreach ($paginator as $group) {
                $result['items'][] = ['role' => 'owner', 'group' => $group];
            }

            return $result;
        }

        $query = $this->getOtherGroupsQuery($userId, $role, $type, $sortColumn, $sortDir, $offset, $limit);

        $paginator = new Paginator($query, false);

        $result = [
            'count' => $paginator->count(),
            'items' => [],
        ];

        foreach ($paginator as $group) {
            /** @var UserInGroup $group */
            $result['items'][] = ['role' => $group->getRole(), 'group' => $group->getGroup()];
        }

        return $result;
    }

    /**
     * @param int $userId
     * @param int $groupId
     *
     * @return array
     */
    public function getUserGroup($userId, $groupId)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->doctrine->getRepository('AppBundle:UserGroup')->createQueryBuilder('g');

        /** @var UserGroup $group */
        $group = $qb
            ->andWhere('g.id = :groupId')
            ->andWhere('g.active = 1')
            ->andWhere('g.owner = :userId')
            ->setParameter('groupId', $groupId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getFirstResult();

        if (!empty($group)) {
            return ['role' => 'owner', 'group' => $group];
        }

        $qb = $this->doctrine->getRepository('AppBundle:UserInGroup')->createQueryBuilder('ug');

        /** @var UserInGroup $group */
        $group = $qb
            ->join('ug.group', 'g')
            ->andWhere('g.id = :groupId')
            ->andWhere('g.active = 1')
            ->andWhere('ug.user = :userId')
            ->setParameter('userId', $userId)
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getFirstResult();

        if (!empty($group)) {
            return ['role' => $group->getRole(), 'group' => $group->getGroup()];
        }

        return [];
    }

    /**
     * @param int    $userId
     * @param string $type
     * @param string $sortColumn
     * @param string $sortDir
     * @param int    $offset
     * @param int    $limit
     *
     * @return \Doctrine\ORM\Query
     */
    private function getOwnerGroupsQuery($userId, $type = null, $sortColumn = 'reference', $sortDir = 'ASC', $offset = null, $limit = null)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->doctrine->getManager()->createQueryBuilder();

        $qb->select('g')->from('AppBundle:UserGroup', 'g');

        if ($type !== null) {
            if ($type === 'admin') {
                $qb->andWhere('g.type = :type')->andWhere('g.id = 1 OR g.parent = 1')->setParameter('type', UserGroup::TYPE_FORMAL);
            } elseif ($type === 'other') {
                $qb->andWhere('g.type != :type')->setParameter('type', UserGroup::TYPE_GROUPHUB);
            } else {
                $qb->andWhere('g.type = :type')->setParameter('type', $type);
            }
        }

        $qb
            ->andWhere('g.active = 1')
            ->andWhere('g.owner = :user')
            ->setParameter('user', $userId)
            ->addOrderBy('g.' . $sortColumn, $sortDir);

        if ($offset !== null && $limit !== null) {
            $qb
                ->setFirstResult($offset)
                ->setMaxResults($limit);
        }

        return $qb->getQuery();
    }

    /**
     * @param int    $userId
     * @param string $role
     * @param string $type
     * @param string $sortColumn
     * @param string $sortDir
     * @param int    $offset
     * @param int    $limit
     *
     * @return \Doctrine\ORM\Query
     */
    private function getOtherGroupsQuery($userId, $role = null, $type = null, $sortColumn = 'reference', $sortDir = 'ASC', $offset = null, $limit = null)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->doctrine->getManager()->createQueryBuilder();

        $qb->select('ug')->from('AppBundle:UserInGroup', 'ug');

        if ($type !== null) {
            if ($type === 'admin') {
                $qb->andWhere('g.type = :type')->andWhere('g.id = 1 OR g.parent = 1')->setParameter('type', UserGroup::TYPE_FORMAL);
            } elseif ($type === 'other') {
                $qb->andWhere('g.type != :type')->setParameter('type', UserGroup::TYPE_GROUPHUB);
            } else {
                $qb->andWhere('g.type = :type')->setParameter('type', $type);
            }
        }

        if ($role !== null) {
            $qb
                ->andWhere('ug.role = :role')
                ->setParameter('role', $role);
        }

        $qb
            ->join('ug.group', 'g')
            ->andWhere('g.active = 1')
            ->andWhere('ug.user = :user')
            ->setParameter('user', $userId)
            ->addOrderBy('ug.role', 'ASC')
            ->addOrderBy('g.' . $sortColumn, $sortDir);

        if ($offset !== null && $limit !== null) {
            $qb
                ->setFirstResult($offset)
                ->setMaxResults($limit);
        }

        return $qb->getQuery();
    }
}
