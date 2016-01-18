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
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class GroupController
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
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="sort property"}
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

        $list = $this->getDoctrine()
            ->getRepository('AppBundle:UserGroup')
            ->findBy(['active' => 1], [$sort => 'ASC'], $limit, $offset);

        return $this->view($list);
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
        $group = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->findOneBy(['id' => $id, 'active' => 1]);
        if ($group === null) {
            throw new NotFoundHttpException('Group with id: ' . $id . ' not found');
        }

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

        $form = $this->createForm(new UserGroupType(), $group);
        $form->submit($request);

        if ($form->isValid()) {

            $group->setActive(1);
            $group->setTimeStamp(new DateTime());

            try {
                $em = $this->getDoctrine()->getManager();

                $em->persist($group);
                $em->flush();

                $this->fireEvent('app.event.group.add', new GroupEvent($group));

                return $this->routeRedirectView('get_group', array('id' => $group->getId()));
            }
            catch (DBALException $e) {
                throw new NotAcceptableHttpException($e->getMessage());
            }
        }

        return $form->getErrors();
    }

    /**
     * Update a single user as a whole. Mind that a PUT requires all user properties included in the JSON object
     *
     * @ApiDoc(
     *   input = "AppBundle\Form\UserGroupType",
     *   resource = true,
     *   requirements = {
     *      {
     *          "name" = "id",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "UserID"
     *      }
     *   },
     *   description="Update a user. Make sure to include all properties!",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found",
     *     500 = "Returned when there is a internal error."
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the user id
     *
     * @return array
     *
     * @throws NotFoundHttpException when note not exist
     */
    public function putGroupAction(Request $request, $id)
    {
        /** @var UserGroup $group */
        $group = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->findOneBy(['id' => $id, 'active' => 1]);
        if ($group === null) {
            throw new NotFoundHttpException('Group with id: ' . $id . ' not found');
        }

        $form = $this->createForm(new UserGroupType(), $group);
        $form->submit($request);

        if ($form->isValid()) {

            try {
                $em = $this->getDoctrine()->getManager();

                $em->persist($group);
                $em->flush();

                $this->fireEvent('app.event.group.update', new GroupEvent($group));

                return $this->routeRedirectView('get_group', array('id' => $group->getId()));
            }
            catch(DBALException $e) {
                throw new NotAcceptableHttpException($e->getMessage());
            }
        }

        return $form->getErrors();
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
     *     404 = "Returned when the user is not found",
     *     500 = "Returned when there is a internal error."
     *   }
     * )

     * @param int $id

     * @return array
     */
    public function deleteGroupAction($id)
    {
        /** @var UserGroup $group */
        $group = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->findOneBy(['id' => $id, 'active' => 1]);
        if ($group === null) {
            throw new NotFoundHttpException('Group with id: ' . $id . ' not found');
        }

        $group->setActive(0);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $this->fireEvent('app.event.group.delete', new GroupEvent($group));

        return $this->routeRedirectView('get_groups');
    }

    /**
     * Retrieve users in a Group.
     *
     * @ApiDoc(
     *  resource = true,
     *  output="ArrayCollection<AppBundle\Entity\User>",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the group is not found.",
     *      500 = "Returned when there is an internal server error"
     *   }
     * )
     *
     * @param int $id
     *
     * @return array
     */
    public function getGroupUsersAction($id)
    {

        if ($this->getDoctrine()->getRepository('AppBundle:UserGroup')->findOneBy(['id' => $id, 'active' => 1]) === null) {
            throw new NotFoundHttpException('Group with id ' . $id . ' does not exists.');
        }

        $list = $this->getDoctrine()
            ->getRepository('AppBundle:UserInGroup')
            ->findBy([ 'groupId' => $id ]);

        return $this->view($list);
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
     * @param Request   $request    Request object.
     * @param int       $id         Group ID
     *
     * @return array
     * @throws NotFoundHttpException when note not exist
     * @throws NotAcceptableHttpException when <input> is not valid.
     */
    public function postGroupUsersAction(Request $request, $id)
    {
        /** @var UserGroup $group */
        $group = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->findOneBy(['id' => $id, 'active' => 1]);
        if ($group === null) {
            throw new NotFoundHttpException('Group with id: ' . $id . ' not found');
        }

        $userInGroup = new UserInGroup();
        $userInGroup->setGroupId($id);
        $form = $this->createForm(new UserInGroupType(), $userInGroup);
        $form->submit($request);

        if ($form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($userInGroup);
                $em->flush();

                $event = new GroupEvent($group);
                $event->setUser($userInGroup);
                $this->fireEvent('app.event.group.useradd', $event);

                return $this->routeRedirectView('get_group_users', array('id' => $group->getId()));
            }
            catch (DBALException $e) {
                throw new NotAcceptableHttpException($e->getMessage());
            }
        }
        return $form->getErrors();
    }

    /**
     * Update a User's role in a group.
     *
     * @ApiDoc(
     *  resource = true,
     *  input="AppBundle\Form\UserInGroupType",
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
     * @param int $groupId Group ID
     * @param int $userId User ID
     * @return array
     */
    public function putGroupUsersAction(Request $request, $groupId, $userId)
    {
        $userInGroup = $this->getDoctrine()
            ->getRepository('AppBundle:UserInGroup')
            ->findBy([ "userId" => $userId, "groupId" => $groupId]);

        if ($userInGroup === null) {
            throw new NotFoundHttpException('User with id ' . $userId . ' in group with id ' . $groupId . ' not found.');
        }

        $userInGroup = new UserInGroup();
        $userInGroup->setGroupId($groupId);
        $userInGroup->setUserId($userId);
        $form = $this->createForm(new UserInGroupUpdateType(), $userInGroup);
        $form->submit($request);

        if ($form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($userInGroup);
                $em->flush();

                /** @var UserGroup $group */
                $group = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->find($groupId);
                $event = new GroupEvent($group);
                $event->setUser($userInGroup);
                $this->fireEvent('app.event.group.userupdate', $event);

                return $this->routeRedirectView('get_group_users', array('id' => $groupId));
            }
            catch (DBALException $e) {
                throw new NotAcceptableHttpException($e->getMessage());
            }
        }
        return $form->getErrors();
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
        $rows = $this->getDoctrine()
            ->getRepository('AppBundle:UserInGroup')
            ->findBy([ "userId" => $userId, "groupId" => $groupId]);

        if (count($rows) !== 1) {
            throw new NotFoundHttpException('User with id ' . $userId . ' in group with id ' . $groupId . ' not found.');
        }

        /** @var UserInGroup $userInGroup */
        $userInGroup = $rows[0];

        $em = $this->getDoctrine()->getManager();
        $em->remove($userInGroup);
        $em->flush();

        /** @var UserGroup $group */
        $group = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->find($groupId);
        $event = new GroupEvent($group);
        $event->setUser($userInGroup);
        $this->fireEvent('app.event.group.userdelete', $event);

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

        $group = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->findOneBy(['id' => $groupId, 'active' => 1]);
        if ($group === null) {
            throw new NotFoundHttpException('Group with id: ' . $groupId . ' not found');
        }

        $rows = $this->getDoctrine()
            ->getRepository('AppBundle:UserGroupInGroup')
            ->findBy(['groupId' => $groupId]);

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
        $rows = $this->getDoctrine()
            ->getRepository('AppBundle:UserGroupInGroup')
            ->findBy([ 'groupId' => $groupId, 'groupInGroupId' => $groupInGroupId ]);

        if (count($rows) !== 1) {
            throw new NotFoundHttpException('Group with id ' . $groupId . ' in groupInGroupId with id ' . $groupInGroupId . ' not found.');
        }

        /** @var UserGroupInGroup $groupInGroup */
        $groupInGroup = $rows[0];

        $em = $this->getDoctrine()->getManager();
        $em->remove($rows[0]);
        $em->flush();

        // Build event object.
        $group = new UserGroup();
        $group->setId($groupId);

        $event = new GroupEvent($group);
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
     * @param int $groupId Group ID
     * @return array
     */
    public function postGroupGroupsAction(Request $request, $groupId) {
        /** @var UserGroup $group */
        $group = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->findOneBy(['id' => $groupId, 'active' => 1]);
        if ($group === null) {
            throw new NotFoundHttpException('Group with id: ' . $groupId . ' not found');
        }

        $groupInGroup = new UserGroupInGroup();
        $groupInGroup->setGroupId($groupId);

        $form = $this->createForm(new UserGroupInGroupType(), $groupInGroup);
        $form->submit($request);

        if ($form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($groupInGroup);
                $em->flush();

                $event = new GroupEvent($group);
                $event->setGroupInGroup($groupInGroup);

                $this->fireEvent('app.event.group.groupadd', $event);

                return $this->routeRedirectView('get_group_users', array('id' => $group->getId()));
            }
            catch (DBALException $e) {
                throw new NotAcceptableHttpException($e->getMessage());
            }
        }
        return $form->getErrors();
    }

    /**
     * Fire UserEvent.
     *
     * @param string $event Event id
     * @param GroupEvent $eventObject
     */
    private function fireEvent($event, GroupEvent $eventObject)
    {
        $this->get('event_dispatcher')->dispatch($event, $eventObject);
    }
}
