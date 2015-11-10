<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserGroup;
use AppBundle\Entity\UserInGroup;
use AppBundle\Form\UserGroupType;
use AppBundle\Form\UserInGroupType;
use AppBundle\Form\UserInGroupUpdateType;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class GroupController
 * @package AppBundle\Controller
 */
class GroupController extends FOSRestController
{
    /**
     * List all groups from the database. Does not support paging!
     *
     * @ApiDoc(
     *  resource = true,
     *  output="ArrayCollection<AppBundle\Entity\UserGroup>",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      500 = "Returned when there is an internal server error"
     *   }
     * )
     *
     * @return array
     */
    public function getGroupsAction()
    {
        $list = $this->getDoctrine()
            ->getRepository('AppBundle:UserGroup')
            ->findAll();

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
     * @param Request $request the request object
     * @param int     $id      the Group id
     *
     * @return array
     *
     * @throws NotFoundHttpException when group not exist
     */
    public function getGroupAction(Request $request, $id)
    {
        $group = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->find($id);
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

            // Created timestamp.
            $group->setTimeStamp(new \DateTime());

            try {
                $em = $this->getDoctrine()->getManager();

                $em->persist($group);
                $em->flush();

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
        $group = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->find($id);
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

     * @param Request $request
     * @param int $id Group ID

     * @return array
     *
     * @throws NotFoundHttpException when note not exist
     */
    public function deleteGroupAction(Request $request, $id)
    {

        $group = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->find($id);
        if ($group === null) {
            throw new NotFoundHttpException('Group with id: ' . $id . ' not found');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($group);
        $em->flush();
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
     * @param Request   $request    Request object.
     * @param int       $id         Group ID
     *
     * @return array
     * @throws NotFoundHttpException when note not exist
     */
    public function getGroupUsersAction(Request $request, $id)
    {

        if ($this->getDoctrine()->getRepository('AppBundle:UserGroup')->find($id) === null) {
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

        $group = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->find($id);
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
     * @param Request $request  Request Object
     * @param int $groupId      Group ID
     * @param int $userId       User ID
     * @return array
     */
    public function deleteGroupUsersAction(Request $request, $groupId, $userId)
    {
        $rows = $this->getDoctrine()
            ->getRepository('AppBundle:UserInGroup')
            ->findBy([ "userId" => $userId, "groupId" => $groupId]);

        if (count($rows) !== 1) {
            throw new NotFoundHttpException('User with id ' . $userId . ' in group with id ' . $groupId . ' not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($rows[0]);
        $em->flush();
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
     * @param Request $request Request object
     * @param int $groupId Group ID
     * @return array
     */
    public function getGroupGroupsAction(Request $request, $groupId)
    {

        $group = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->find($groupId);
        if ($group === null) {
            throw new NotFoundHttpException('Group with id: ' . $groupId . ' not found');
        }

        $rows = $this->getDoctrine()
            ->getRepository('AppBundle:UserGroupInGroup')
            ->findBy(['groupId' => $groupId]);

        return $this->view($rows);
    }
}
