<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserGroup;
use AppBundle\Entity\UserGroupInGroup;
use AppBundle\Entity\UserInGroup;
use AppBundle\Event\GroupEvent;
use AppBundle\Form\UserGroupInGroupType;
use AppBundle\Form\UserGroupType;
use AppBundle\Form\UserInGroupType;
use AppBundle\Form\UserInGroupUpdateType;
use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class GroupController
 *
 * @todo: Introduce service layer, and move most logic there...
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GroupController extends FOSRestController
{
    /**
     * List all groups from the database.
     *
     * @ApiDoc(
     *  resource = true,
     *  parameters = {
     *      {"name"="offset", "dataType"="integer", "required"=false, "description"="offset for retrieving resources"},
     *      {"name"="limit", "dataType"="integer", "required"=false, "description"="limit for retrieving resources"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="sort property, prefix with '-' to change the order"},
     *      {"name"="type", "dataType"="string", "required"=false, "description"="type filter, either 'ldap', '!ldap' or 'formal'"},
     *      {"name"="query", "dataType"="string", "required"=false, "description"="search filter"}
     *      {"name"="ids", "dataType"="array", "required"=false, "description"="a list of id's to retrieve"}
     *  },
     *  output="ArrayCollection<AppBundle\Entity\UserGroup>",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      500 = "Returned when there is an internal server error"
     *   }
     * )
     *
     * @param Request $request
     *
     * @return array
     */
    public function getGroupsAction(Request $request)
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 100);
        $sort = $request->query->get('sort', 'reference');
        $type = $request->query->get('type');
        $query = $request->query->get('query');
        $ids = $request->query->get('ids');

        /** @var QueryBuilder $qb */
        $qb = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->createQueryBuilder('g');

        $typeFilter = '1 = 1';

        $sortDir = 'ASC';
        if ($sort[0] === '-') {
            $sortDir = 'DESC';
            $sort = substr($sort, 1);
        }

        if ($type === 'ldap') {
            $typeFilter = 'g.type = \'ldap\'';
        }

        if ($type === '!ldap') {
            $typeFilter = 'g.type != \'ldap\'';
        }

        if ($type === 'formal') {
            $typeFilter = 'g.type = \'formal\'';
        }

        $queryFilter = '1 = 1';
        if (!empty($query)) {
            $queryFilter = $qb->expr()->orX(
                $qb->expr()->like('g.name', ':query'),
                $qb->expr()->like('g.description', ':query')
            );

            $qb->setParameter('query', '%'.$query.'%');
        }

        if (!empty($ids)) {
            $qb->andWhere($qb->expr()->in('g.id', ':groups'));
            $qb->setParameter('groups', $ids);

            $offset = 0;
            $limit = count($ids);
        }

        $query = $qb
            ->andWhere('g.active = 1')
            ->andWhere($typeFilter)
            ->andWhere($queryFilter)
            ->orderBy('g.' . $sort, $sortDir)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query);

        $result = [
            'count' => $paginator->count(),
            'items' => $paginator->getIterator()->getArrayCopy(),
        ];

        return $this->view($result);
    }

    /**
     * Retrieve a single group from database by group ID.
     *
     * @ApiDoc(
     *   output = "AppBundle\Entity\UserGroup",
     *   resource = true,
     *   requirements = {
     *      {
     *          "name" = "id",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "GroupID"
     *      }
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the group is not found",
     *     500 = "Returned when there is a internal error."
     *   }
     * )
     *
     * @param int $id
     *
     * @return array
     */
    public function getGroupAction($id)
    {
        $group = $this->getGroup($id);

        return $this->view($group);
    }

    /**
     * Creates a new group from the submitted JSON data.
     *
     * @ApiDoc(
     *   input = "AppBundle\Form\UserGroupType",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     406 = "Returned when the group is invalid",
     *     500 = "Returned when there is a internal error."
     *   }
     * )
     *
     * @param Request $request the request object
     *
     * @return array
     */
    public function postGroupsAction(Request $request)
    {
        $group = new UserGroup();

        $form = $this->createForm(UserGroupType::class, $group);
        $form->submit($request);

        if ($form->isValid()) {

            $group->setActive(1);
            $group->setTimestamp(new DateTime());

            try {
                $em = $this->getDoctrine()->getManager();

                $em->persist($group);
                $em->flush();

                $this->fireEvent('app.event.group.add', new GroupEvent($group));

                return $this->view($group, Response::HTTP_CREATED);
            } catch (DBALException $e) {
                throw new NotAcceptableHttpException($e->getMessage());
            }
        }

        return $form;
    }

    /**
     * Update a single group as a whole. Mind that a PUT requires all group properties included in the JSON object
     *
     * @ApiDoc(
     *   input = "AppBundle\Form\UserGroupType",
     *   resource = true,
     *   requirements = {
     *      {
     *          "name" = "id",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "GroupID"
     *      }
     *   },
     *   description="Update a group. Make sure to include all properties!",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the group is not found",
     *     500 = "Returned when there is a internal error."
     *   }
     * )
     *
     * @param Request $request
     * @param int     $id
     *
     * @return array
     */
    public function putGroupAction(Request $request, $id)
    {
        $group = $this->getGroup($id);

        $form = $this->createForm(UserGroupType::class, $group);
        $form->submit($request);

        if ($form->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();

                $this->fireEvent('app.event.group.update', new GroupEvent($group));

                return $this->routeRedirectView('get_group', ['id' => $group->getId()]);
            } catch (DBALException $e) {
                throw new NotAcceptableHttpException($e->getMessage());
            }
        }

        return $form;
    }

    /**
     * Update a single group as a whole. Mind that a PATCH only updates the specified attributes
     *
     * @ApiDoc(
     *   input = "AppBundle\Form\UserGroupType",
     *   resource = true,
     *   requirements = {
     *      {
     *          "name" = "id",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "GroupID"
     *      }
     *   },
     *   description="Update a group.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the group is not found",
     *     500 = "Returned when there is a internal error."
     *   }
     * )
     *
     * @param Request $request
     * @param int     $id
     *
     * @return array
     */
    public function patchGroupAction(Request $request, $id)
    {
        $group = $this->getGroup($id);

        $form = $this->createForm(UserGroupType::class, $group);
        $form->submit($request, false);

        if ($form->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();

                $this->fireEvent('app.event.group.update', new GroupEvent($group));

                return $this->routeRedirectView('get_group', ['id' => $group->getId()]);
            } catch (DBALException $e) {
                throw new NotAcceptableHttpException($e->getMessage());
            }
        }

        return $form;
    }


    /**
     * Delete group from the database by group ID.
     *
     * @ApiDoc(
     *   resource = true,
     *   requirements = {
     *      {
     *          "name" = "id",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "GroupID"
     *      }
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the group is not found",
     *     500 = "Returned when there is a internal error."
     *   }
     * )
     *
     * @param int $id
     *
     * @return array
     */
    public function deleteGroupAction($id)
    {
        $group = $this->getGroup($id);

        $children = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->findBy(['parent' => $id, 'active' => 1]);
        if (!empty($children)) {
            throw new BadRequestHttpException('Parent groups cannot be disabled');
        }

        $group->setActive(0);

        $this->getDoctrine()->getManager()->flush();

        $this->fireEvent('app.event.group.delete', new GroupEvent($group));

        return $this->routeRedirectView('get_groups');
    }

    /**
     * Retrieve users in a Group.
     *
     * @ApiDoc(
     *  resource = true,
     *  parameters = {
     *      {"name"="offset", "dataType"="integer", "required"=false, "description"="offset for retrieving resources"},
     *      {"name"="limit", "dataType"="integer", "required"=false, "description"="limit for retrieving resources"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="sort property, prefix with '-' to change the order"},
     *      {"name"="query", "dataType"="string", "required"=false, "description"="search filter"}
     *      {"name"="users", "dataType"="array", "required"=false, "description"="array with user ids"},
     *      {"name"="role", "dataType"="string", "required"=false, "description"="role filter"}
     *  },
     *  output="ArrayCollection<AppBundle\Entity\User>",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the group is not found.",
     *      500 = "Returned when there is an internal server error"
     *   }
     * )
     *
     * @param Request $request
     * @param int     $id
     *
     * @return array
     */
    public function getGroupUsersAction(Request $request, $id)
    {
        $this->getGroup($id);

        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 100);
        $sort = $request->query->get('sort', 'reference');
        $query = $request->query->get('query');
        $users = (array)$request->query->get('users');
        $role = $request->query->get('role');

        /** @var QueryBuilder $qb */
        $qb = $this->getDoctrine()->getRepository('AppBundle:UserInGroup')->createQueryBuilder('ug');

        if (!empty($query)) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('u.firstName', ':query'),
                    $qb->expr()->like('u.lastName', ':query'),
                    $qb->expr()->like('u.loginName', ':query')
                )
            );

            $qb->setParameter('query', '%'.$query.'%');
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

        $qb
            ->andWhere('ug.group = :id')
            ->setParameter('id', $id)
            ->join('ug.user', 'u')
            ->orderBy('u.' . $sort, 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $qb->getQuery();

        $paginator = new Paginator($query, false);

        $result = [
            'count' => $paginator->count(),
            'items' => $paginator->getIterator()->getArrayCopy(),
        ];


        return $this->view($result);
    }

    /**
     * Add a user to a group.
     *
     * @ApiDoc(
     *  resource = true,
     *  input="AppBundle\Form\UserInGroupType",
     *  requirements = {
     *      {
     *          "name" = "id",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "GroupID"
     *      }
     *  },
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the group is not found.",
     *      406 = "Returned when the user is invalid.",
     *      500 = "Returned when there is a internal error."
     *  }
     * )
     *
     * @param Request $request Request object.
     * @param int     $id      Group ID
     *
     * @return array
     * @throws NotFoundHttpException when note not exist
     * @throws NotAcceptableHttpException when <input> is not valid.
     */
    public function postGroupUsersAction(Request $request, $id)
    {
        /** @var UserGroup $group */
        $group = $this->getGroup($id);

        $userInGroup = new UserInGroup();
        $userInGroup->setGroup($group);

        $form = $this->createForm(UserInGroupType::class, $userInGroup);
        $form->submit($request);

        if ($form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($userInGroup);
                $em->flush();

                $event = new GroupEvent($group);
                $event->setUser($userInGroup);
                $event->setMessage($form->get('message')->getData());
                $this->fireEvent('app.event.group.useradd', $event);

                return $this->routeRedirectView('get_group_users', ['id' => $group->getId()]);
            } catch (DBALException $e) {
                throw new NotAcceptableHttpException($e->getMessage());
            }
        }

        return $form;
    }

    /**
     * Update a User's role in a group.
     *
     * @ApiDoc(
     *  resource = true,
     *  input="AppBundle\Form\UserInGroupUpdateType",
     *  requirements = {
     *      {
     *          "name" = "groupId",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "GroupID"
     *      },
     *      {
     *          "name" = "userId",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "UserID"
     *      }
     *  },
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the group is not found.",
     *      406 = "Returned when the user is invalid.",
     *      500 = "Returned when there is a internal error."
     *  }
     * )
     *
     * @param Request $request
     * @param int     $groupId Group ID
     * @param int     $userId  User ID
     *
     * @return array
     */
    public function putGroupUsersAction(Request $request, $groupId, $userId)
    {
        $userInGroup = $this->getUserInGroup($groupId, $userId);

        $form = $this->createForm(new UserInGroupUpdateType(), $userInGroup);
        $form->submit($request);

        if ($form->isValid()) {
            $this->get('app.manager.membership')->updateMembership($userInGroup);

            return $this->routeRedirectView('get_group_users', ['id' => $groupId]);
        }

        return $form;
    }

    /**
     * Remove a user from a group.
     *
     * @ApiDoc(
     *  resource = true,
     *  requirements = {
     *      {
     *          "name" = "groupId",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "GroupID"
     *      },
     *      {
     *          "name" = "userId",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "UserID"
     *      }
     *  },
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the group or user is not found.",
     *      500 = "Returned when there is a internal error."
     *  }
     * )
     *
     * @param int $groupId
     * @param int $userId
     *
     * @return array
     */
    public function deleteGroupUsersAction($groupId, $userId)
    {
        $userInGroup = $this->getUserInGroup($groupId, $userId);

        $this->get('app.manager.membership')->deleteMembership($userInGroup);

        return $this->routeRedirectView('get_groups');
    }

    /**
     * List child groups in a Group.
     *
     * @ApiDoc(
     *  resource = true,
     *  output="ArrayCollection<AppBundle\Entity\Group>",
     *  requirements = {
     *      {
     *          "name" = "id",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "GroupID"
     *      }
     *  },
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the group is not found.",
     *      500 = "Returned when there is an internal server error"
     *   }
     * )
     *
     * @param int $groupId
     *
     * @return array
     */
    public function getGroupGroupsAction($groupId)
    {
        $this->getGroup($groupId);

        $rows = $this->getDoctrine()->getRepository('AppBundle:UserGroupInGroup')->findBy(['group' => $groupId]);

        return $this->view($rows);
    }

    /**
     * Remove a group from a group.
     *
     * @ApiDoc(
     *  resource = true,
     *  requirements = {
     *      {
     *          "name" = "groupId",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "GroupID"
     *      },
     *      {
     *          "name" = "groupInGroupId",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "The group that is in this group."
     *      }
     *  },
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the group is not found.",
     *      500 = "Returned when there is a internal error."
     *  }
     * )
     *
     * @param int $groupId
     * @param int $groupInGroupId
     *
     * @return array
     */
    public function deleteGroupGroupsAction($groupId, $groupInGroupId)
    {
        /** @var UserGroupInGroup $groupInGroup */
        $groupInGroup = $this->getDoctrine()->getRepository('AppBundle:UserGroupInGroup')->findOneBy(
            ['group' => $groupId, 'groupInGroup' => $groupInGroupId]
        );

        if ($groupInGroup === null) {
            throw new NotFoundHttpException(
                'Group with id ' . $groupId . ' in groupInGroupId with id ' . $groupInGroupId . ' not found.'
            );
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($groupInGroup);
        $em->flush();

        $event = new GroupEvent($groupInGroup->getGroup());
        $event->setGroupInGroup($groupInGroup);
        $this->fireEvent('app.event.group.groupdelete', $event);

        return $this->routeRedirectView('get_groups');
    }

    /**
     * Add a group to a group
     *
     * @ApiDoc(
     *  resource = true,
     *  input="AppBundle\Form\UserGroupInGroupType",
     *  requirements = {
     *      {
     *          "name" = "groupId",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "GroupID"
     *      }
     *  },
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the group is not found.",
     *      500 = "Returned when there is an internal server error"
     *   }
     * )
     *
     * @param Request $request Request object
     * @param int     $groupId Group ID
     *
     * @return array
     */
    public function postGroupGroupsAction(Request $request, $groupId)
    {
        $group = $this->getGroup($groupId);

        $groupInGroup = new UserGroupInGroup();
        $groupInGroup->setGroup($group);

        $form = $this->createForm(UserGroupInGroupType::class, $groupInGroup);
        $form->submit($request);

        if ($form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($groupInGroup);
                $em->flush();

                $event = new GroupEvent($group);
                $event->setGroupInGroup($groupInGroup);
                $this->fireEvent('app.event.group.groupadd', $event);

                return $this->routeRedirectView('get_group_users', ['id' => $group->getId()]);
            } catch (DBALException $e) {
                throw new NotAcceptableHttpException($e->getMessage());
            }
        }

        return $form;
    }

    /**
     * Fire UserEvent.
     *
     * @param string     $event Event id
     * @param GroupEvent $eventObject
     */
    private function fireEvent($event, GroupEvent $eventObject)
    {
        $this->get('event_dispatcher')->dispatch($event, $eventObject);
    }

    /**
     * @param int $id
     *
     * @return UserGroup
     */
    private function getGroup($id)
    {
        $group = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->findOneBy(['id' => $id, 'active' => 1]);

        if ($group === null) {
            throw new NotFoundHttpException('Group with id: ' . $id . ' not found');
        }

        return $group;
    }

    /**
     * @param int $groupId
     * @param int $userId
     *
     * @return UserInGroup
     */
    private function getUserInGroup($groupId, $userId)
    {
        $userInGroup = $this->get('app.manager.membership')->findMembership($userId, $groupId);

        if ($userInGroup === null) {
            throw $this->createNotFoundException(
                'User with id ' . $userId . ' in group with id ' . $groupId . ' not found.'
            );
        }

        return $userInGroup;
    }
}
