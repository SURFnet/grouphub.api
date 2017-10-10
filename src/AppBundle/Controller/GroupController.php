<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserGroup;
use AppBundle\Entity\UserGroupInGroup;
use AppBundle\Entity\UserInGroup;
use AppBundle\Form\UserGroupInGroupType;
use AppBundle\Form\UserGroupType;
use AppBundle\Form\UserInGroupType;
use AppBundle\Form\UserInGroupUpdateType;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="sort property, prefix with '-' to change the order"},
     *      {"name"="type", "dataType"="string", "required"=false, "description"="type filter, either 'ldap', '!ldap' or 'formal'"},
     *      {"name"="query", "dataType"="string", "required"=false, "description"="search filter"},
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

        $sortDir = 'ASC';
        if ($sort[0] === '-') {
            $sortDir = 'DESC';
            $sort = substr($sort, 1);
        }

        $result = $this->get('app.manager.group')->findGroups($query, $type, $sort, $sortDir, $offset, $limit, $ids);

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
            $this->get('app.manager.group')->addGroup($group);

            return $this->view($group, Response::HTTP_CREATED);
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
            $this->get('app.manager.group')->updateGroup($group);

            return $this->routeRedirectView('get_group', ['id' => $group->getId()]);
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
            $this->get('app.manager.group')->updateGroup($group);

            return $this->routeRedirectView('get_group', ['id' => $group->getId()]);
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

        $this->get('app.manager.group')->deleteGroup($group);

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
     *      {"name"="query", "dataType"="string", "required"=false, "description"="search filter"},
     *      {"name"="users", "dataType"="array", "required"=false, "description"="array with user ids"},
     *      {"name"="roles", "dataType"="array", "required"=false, "description"="role filter"}
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
        $roles = $request->query->get('roles');

        $result = $this->get('app.manager.membership')->findMemberships($id, $query, $roles, $users, $sort, $offset, $limit);

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
     */
    public function postGroupUsersAction(Request $request, $id)
    {
        $group = $this->getGroup($id);

        $userInGroup = new UserInGroup();
        $userInGroup->setGroup($group);

        $form = $this->createForm(UserInGroupType::class, $userInGroup);
        $form->submit($request);

        if ($form->isValid()) {
            $this->get('app.manager.membership')->addMembership($userInGroup, $form->get('message')->getData());

            return $this->routeRedirectView('get_group_users', ['id' => $group->getId()]);
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

        $result = $this->get('app.manager.group_in_group')->findGroupInGroups($groupId);

        return $this->view($result);
    }

    /**
     * List linkable groups for a Group.
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
     *      },{
     *          "name"="query",
     *          "dataType"="string",
     *          "required"=false,
     *          "description"="search filter"
     *      },
     *      {
     *          "name"="offset",
     *          "dataType"="integer",
     *          "required"=false,
     *          "description"="offset for retrieving resources"
     *      },
     *      {
     *          "name"="limit",
     *          "dataType"="integer",
     *          "required"=false,
     *          "description"="limit for retrieving resources"
     *      }
     *  },
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      500 = "Returned when there is an internal server error"
     *   }
     * )
     *
     * @param int $groupId
     * @param Request $request
     *
     * @return array
     */
    public function getGroupGroupsLinkableAction($groupId, Request $request)
    {
        $this->getGroup($groupId);

        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 12);
        $query = $request->query->get('query');

        $result = $this->get('app.manager.group')->findGroupsLinkableToGroup(
            $groupId,
            $query,
            'reference',
            'ASC',
            $offset,
            $limit
        );

        return $this->view($result);
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
        $groupInGroup = $this->get('app.manager.group_in_group')->findGroupInGroup($groupId, $groupInGroupId);

        if ($groupInGroup === null) {
            throw new NotFoundHttpException(
                'Group with id ' . $groupId . ' in groupInGroupId with id ' . $groupInGroupId . ' not found.'
            );
        }

        $this->get('app.manager.group_in_group')->deleteGroupInGroup($groupInGroup);

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
            $this->get('app.manager.group_in_group')->addGroupInGroup($groupInGroup);

            return $this->routeRedirectView('get_group_users', ['id' => $group->getId()]);
        }

        return $form;
    }

    /**
     * @param int $id
     *
     * @return UserGroup
     */
    private function getGroup($id)
    {
        $group = $this->get('app.manager.group')->findGroup($id);

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
