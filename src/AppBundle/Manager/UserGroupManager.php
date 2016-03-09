<?php

namespace AppBundle\Manager;

use AppBundle\Entity\UserInGroup;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\QueryBuilder;

/**
 * UserGroupManager
 *
 * @todo: implement, merge
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
     *
     * @return array
     */
    public function findUserGroups($userId, $type = null, $sortColumn = 'reference', $sortDir = 'ASC')
    {
        $result = [];

        /** @var QueryBuilder $qb */
        $qb = $this->doctrine->getManager()->createQueryBuilder();

        if ($type === 'formal') {
            $qb->andWhere('g.type = \'formal\'');
        }


        $qb->select('g')->from('AppBundle:UserGroup', 'g');

        $owned = $qb
            ->andWhere('g.active = 1')
            ->andWhere('g.owner = :user')
            ->setParameter('user', $userId)
            ->addOrderBy('g.' . $sortColumn, $sortDir)
            ->getQuery()
            ->getResult();

        foreach ($owned as $group) {
            $result[] = ['role' => 'owner', 'group' => $group];
        }

        $qb->select('ug')->from('AppBundle:UserInGroup', 'ug');

        /** @var UserInGroup[] $other */
        $other = $qb
            ->join('ug.group', 'g')
            ->andWhere('g.active = 1')
            ->andWhere('ug.user = :user')
            ->setParameter('user', $userId)
            ->addOrderBy('ug.role', 'ASC')
            ->addOrderBy('g.' . $sortColumn, $sortDir)
            ->getQuery()
            ->getResult();

        foreach ($other as $group) {
            $result[] = ['role' => $group->getRole(), 'group' => $group->getGroup()];
        }

        return $result;
    }

    /**
     * @param        $userId
     * @param null   $type
     * @param string $sortColumn
     * @param string $sortDir
     * @param int    $offset
     * @param int    $limit
     *
     * @return array of collections
     */
    public function findUserGroupsGroupedByTypeAndRole($userId, $type = null, $sortColumn = 'reference', $sortDir = 'ASC', $offset = 0, $limit = 10)
    {

    }

    /**
     * @param        $userId
     * @param        $role
     * @param null   $type
     * @param string $sortColumn
     * @param string $sortDir
     * @param int    $offset
     * @param int    $limit
     *
     * @return array collection
     */
    public function findUserGroupsForRole($userId, $role, $type = null, $sortColumn = 'reference', $sortDir = 'ASC', $offset = 0, $limit = 10)
    {
        $result = [];

        /** @var QueryBuilder $qb */
        $qb = $this->doctrine->getManager()->createQueryBuilder();

        if ($type === 'formal') {
            $qb->andWhere('g.type = \'formal\'');
        }

        if ($role === 'owner') {
            $qb->select('g')->from('AppBundle:UserGroup', 'g');

            $owned = $qb
                ->andWhere('g.active = 1')
                ->andWhere('g.owner = :user')
                ->setParameter('user', $userId)
                ->addOrderBy('g.' . $sortColumn, $sortDir)
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();

            foreach ($owned as $group) {
                $result[] = ['role' => 'owner', 'group' => $group];
            }

            return $result;
        }

        $qb->select('ug')->from('AppBundle:UserInGroup', 'ug');

        /** @var UserInGroup[] $other */
        $other = $qb
            ->join('ug.group', 'g')
            ->andWhere('g.active = 1')
            ->andWhere('ug.user = :user')
            ->setParameter('user', $userId)
            ->andWhere('ug.role = :role')
            ->setParameter('role', $role)
            ->addOrderBy('ug.role', 'ASC')
            ->addOrderBy('g.' . $sortColumn, $sortDir)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        foreach ($other as $group) {
            $result[] = ['role' => $group->getRole(), 'group' => $group->getGroup()];
        }

        return $result;
    }
}
