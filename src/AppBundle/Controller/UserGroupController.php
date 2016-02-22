<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserGroup;
use AppBundle\Entity\UserInGroup;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

/**
 * Rest controller for users.
 */
class UserGroupController extends FOSRestController
{
    /**
     * List all roles and groups a user belongs to.
     *
     * @ApiDoc(
     *  resource = true,
     *  requirements = {
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "UserID"
     *     }
     *  },
     *  output="array<AppBundle\Entity\UserInGroup>",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      500 = "Returned when there is an internal server error"
     *   }
     * )
     *
     * @param int     $id
     * @param Request $request
     *
     * @return array
     */
    public function getUserGroupsAction($id, Request $request)
    {
        $sort = $request->query->get('sort', 'reference');

        $sortDir = 'ASC';
        if ($sort[0] === '-') {
            $sortDir = 'DESC';
            $sort = substr($sort, 1);
        }

        /** @var UserGroup[] $other */
        $owned = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->createQueryBuilder('g')
            ->andWhere('g.active = 1')
            ->andWhere('g.owner = :user')
            ->setParameter('user', $id)
            ->addOrderBy('g.' . $sort, $sortDir)
            ->getQuery()
            ->getResult();

        /** @var UserInGroup[] $other */
        $other = $this->getDoctrine()->getRepository('AppBundle:UserInGroup')->createQueryBuilder('ug')
            ->join('ug.group', 'g')
            ->andWhere('g.active = 1')
            ->andWhere('ug.user = :user')
            ->setParameter('user', $id)
            ->addOrderBy('ug.role', 'ASC')
            ->addOrderBy('g.' . $sort, $sortDir)
            ->getQuery()
            ->getResult();

        $result = [];

        foreach ($owned as $group) {
            $result[] = ['role' => 'owner', 'group' => $group];
        }

        foreach ($other as $group) {
            $result[] = ['role' => $group->getRole(), 'group' => $group->getGroup()];
        }

        return $this->view($result);
    }

    /**
     * Get the role for a group a user belongs to.
     *
     * @ApiDoc(
     *  resource = true,
     *  requirements = {
     *     {
     *         "name" = "userId",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "UserID"
     *     },
     *     {
     *         "name" = "groupId",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "GroupID"
     *     }
     *  },
     *  output="AppBundle\Entity\UserInGroup",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      500 = "Returned when there is an internal server error"
     *   }
     * )
     *
     * @param int $userId
     * @param int $groupId
     *
     * @return array
     */
    public function getUserGroupAction($userId, $groupId)
    {
        /** @var UserGroup[] $other */
        $owned = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->createQueryBuilder('g')
            ->andWhere('g.id = :groupId')
            ->andWhere('g.active = 1')
            ->andWhere('g.owner = :userId')
            ->setParameter('groupId', $groupId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();

        if (!empty($owned[0])) {
            $owned = $owned[0];

            return $this->view(['role' => 'owner', 'group' => $owned]);
        }

        /** @var UserInGroup[] $other */
        $other = $this->getDoctrine()->getRepository('AppBundle:UserInGroup')->createQueryBuilder('ug')
            ->join('ug.group', 'g')
            ->andWhere('g.id = :groupId')
            ->andWhere('g.active = 1')
            ->andWhere('ug.user = :userId')
            ->setParameter('userId', $userId)
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getResult();

        if (!empty($other[0])) {
            /** @var UserInGroup $other */
            $other = $other[0];

            return $this->view(['role' => $other->getRole(), 'group' => $other->getGroup()]);
        }

        return $this->view();
    }
}
