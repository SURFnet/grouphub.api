<?php

namespace AppBundle\Controller;

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
     *  parameters = {
     *      {"name"="offset", "dataType"="integer", "required"=false, "description"="offset for retrieving resources"},
     *      {"name"="limit", "dataType"="integer", "required"=false, "description"="limit for retrieving resources"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="sort property used when retrieving resources"},
     *      {"name"="type", "dataType"="string", "required"=false, "description"="type filter"},
     *      {"name"="groups", "dataType"="array", "required"=false, "description"="group id filter"}
    *   },
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
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 10);
        $sort = $request->query->get('sort', 'reference');
        $type = $request->query->get('type');
        $groups = (array)$request->query->get('groups');

        $sortDir = 'ASC';
        if ($sort[0] === '-') {
            $sortDir = 'DESC';
            $sort = substr($sort, 1);
        }

        $result = $this->get('app.manager.user_group')->findUserGroups($id, $type, $sort, $sortDir, $offset, $limit, $groups);

        return $this->view($result);
    }

    /**
     * List all roles and groups a user belongs to in a grouped 'view'
     *
     * @ApiDoc(
     *  parameters = {
     *      {"name"="offset", "dataType"="integer", "required"=false, "description"="offset for retrieving resources"},
     *      {"name"="limit", "dataType"="integer", "required"=false, "description"="limit for retrieving resources"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="sort property used when retrieving resources"},
     *      {"name"="type", "dataType"="string", "required"=false, "description"="type filter"}
     *   },
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
    public function getUserGroupsGroupedAction($id, Request $request)
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 10);
        $sort = $request->query->get('sort', 'reference');
        $type = $request->query->get('type');

        $sortDir = 'ASC';
        if ($sort[0] === '-') {
            $sortDir = 'DESC';
            $sort = substr($sort, 1);
        }

        $result = $this->get('app.manager.user_group')->findUserGroupsGroupedByTypeAndRole(
            $id,
            $type,
            $sort,
            $sortDir,
            $offset,
            $limit
        );

        return $this->view($result);
    }

    /**
     * List all groups a user owns
     *
     * @ApiDoc(
     *  parameters = {
     *      {"name"="offset", "dataType"="integer", "required"=false, "description"="offset for retrieving resources"},
     *      {"name"="limit", "dataType"="integer", "required"=false, "description"="limit for retrieving resources"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="sort property used when retrieving resources"},
     *      {"name"="type", "dataType"="string", "required"=false, "description"="type filter"}
     *   },
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
    public function getUserGroupsOwnerAction($id, Request $request)
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 10);
        $sort = $request->query->get('sort', 'reference');
        $type = $request->query->get('type');

        $sortDir = 'ASC';
        if ($sort[0] === '-') {
            $sortDir = 'DESC';
            $sort = substr($sort, 1);
        }

        $result = $this->get('app.manager.user_group')->findUserGroupsForRole(
            $id,
            'owner',
            $type,
            $sort,
            $sortDir,
            $offset,
            $limit
        );

        return $this->view($result);
    }

    /**
     * List all admin groups of a user.
     *
     * @ApiDoc(
     *  parameters = {
     *      {"name"="offset", "dataType"="integer", "required"=false, "description"="offset for retrieving resources"},
     *      {"name"="limit", "dataType"="integer", "required"=false, "description"="limit for retrieving resources"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="sort property used when retrieving resources"},
     *      {"name"="type", "dataType"="string", "required"=false, "description"="type filter"}
     *   },
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
    public function getUserGroupsAdminAction($id, Request $request)
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 10);
        $sort = $request->query->get('sort', 'reference');
        $type = $request->query->get('type');

        $sortDir = 'ASC';
        if ($sort[0] === '-') {
            $sortDir = 'DESC';
            $sort = substr($sort, 1);
        }

        $result = $this->get('app.manager.user_group')->findUserGroupsForRole(
            $id,
            UserInGroup::ROLE_ADMIN,
            $type,
            $sort,
            $sortDir,
            $offset,
            $limit
        );

        return $this->view($result);
    }

    /**
     * List all member groups of a user.
     *
     * @ApiDoc(
     *  parameters = {
     *      {"name"="offset", "dataType"="integer", "required"=false, "description"="offset for retrieving resources"},
     *      {"name"="limit", "dataType"="integer", "required"=false, "description"="limit for retrieving resources"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="sort property used when retrieving resources"},
     *      {"name"="type", "dataType"="string", "required"=false, "description"="type filter"}
     *   },
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
    public function getUserGroupsMemberAction($id, Request $request)
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 10);
        $sort = $request->query->get('sort', 'reference');
        $type = $request->query->get('type');

        $sortDir = 'ASC';
        if ($sort[0] === '-') {
            $sortDir = 'DESC';
            $sort = substr($sort, 1);
        }

        $result = $this->get('app.manager.user_group')->findUserGroupsForRole(
            $id,
            UserInGroup::ROLE_MEMBER,
            $type,
            $sort,
            $sortDir,
            $offset,
            $limit
        );

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
        $group = $this->get('app.manager.user_group')->getUserGroup($userId, $groupId);

        return $this->view($group);
    }
}
