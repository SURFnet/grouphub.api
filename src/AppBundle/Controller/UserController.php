<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Event\UserEvent;
use AppBundle\Form\UserType;

use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Rest controller for users.
 */
class UserController extends FOSRestController
{
    /**
     * List all users from the database.
     *
     * @ApiDoc(
     *  resource = true,
     *  parameters = {
     *      {"name"="offset", "dataType"="int", "required"=false, "description"="offset for retrieving resources"},
     *      {"name"="limit", "dataType"="int", "required"=false, "description"="limit for retrieving resources"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="sort property"}
     *  },
     *  output="ArrayCollection<AppBundle\Entity\User>",
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
    public function getUsersAction(Request $request)
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 100);
        $sort = $request->query->get('sort', 'reference');

        $list =  $this->getDoctrine()->getRepository('AppBundle:User')->createQueryBuilder('u')
            ->where('u.type = \'ldap\'')
            ->orderBy('u.' . $sort, 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $this->view($list);
    }

    /**
     * Retrieve a single user from database by user ID.
     *
     * @ApiDoc(
     *   output = "AppBundle\Entity\User",
     *   resource = true,
     *   requirements = {
     *      {
     *          "name" = "id",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "UserID"
     *      }
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param int $id
     *
     * @return array
     */
    public function getUserAction($id)
    {
        $user = $this->getGrouphubUser($id);

        return $this->view($user);
    }

    /**
     * Creates a new user from the submitted JSON data.
     *
     * @ApiDoc(
     *   input = "AppBundle\Form\UserType",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is invalid"
     *   }
     * )
     *
     * @param Request $request the request object
     *
     * @return array
     * @throws NotAcceptableHttpException
     */
    public function postUsersAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(new UserType(), $user);
        $form->submit($request);

        if ($form->isValid()) {

            // Created timestamp.
            $user->setTimeStamp(new \DateTime());

            try {
                $em = $this->getDoctrine()->getManager();

                $em->persist($user);
                $em->flush();

                $this->fireEvent('app.event.user.add', new UserEvent($user));

                return $this->routeRedirectView('get_user', ['id' => $user->getId()]);
            } catch (DBALException $e) {
                throw new NotAcceptableHttpException($e->getMessage());
            }
        }

        return $form;
    }

    /**
     * Update a single user as a whole. Mind that a PUT requires all user properties included in the JSON object
     *
     * @ApiDoc(
     *   input = "AppBundle\Form\UserType",
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
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the user id
     *
     * @return array
     *
     * @throws NotFoundHttpException when user not exist
     */
    public function putUserAction(Request $request, $id)
    {
        $user = $this->getGrouphubUser($id);

        $form = $this->createForm(new UserType(), $user);
        $form->submit($request);

        if ($form->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();

                $this->fireEvent('app.event.user.update', new UserEvent($user));

                return $this->routeRedirectView('get_user', ['id' => $user->getId()]);
            } catch (DBALException $e) {
                throw new NotAcceptableHttpException($e->getMessage());
            }
        }

        return $form;
    }

    /**
     * Delete user from the database by user ID.
     *
     * @ApiDoc(
     *   resource = true,
     *   requirements = {
     *      {
     *          "name" = "id",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "UserID"
     *      }
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param int $id
     *
     * @return array
     */
    public function deleteUserAction($id)
    {
        $user = $this->getGrouphubUser($id);

        $doctrine = $this->getDoctrine();

        $ownedGroups = $doctrine->getRepository('AppBundle:UserGroup')->findBy(['owner' => $user]);
        foreach ($ownedGroups as $group) {
            $group->setActive(0);
            $group->setOwner(
                $doctrine->getRepository('AppBundle:User')->findOneBy(['reference' => User::REFERENCE_TRASH])
            );
        }

        $this->fireEvent('app.event.user.delete', new UserEvent($user));

        $em = $doctrine->getManager();
        $em->remove($user);
        $em->flush();

        return $this->routeRedirectView('get_users');
    }

    /**
     * Fire UserEvent.
     *
     * @param string    $event Event id
     * @param UserEvent $eventObject
     */
    private function fireEvent($event, UserEvent $eventObject)
    {
        $this->get('event_dispatcher')->dispatch($event, $eventObject);
    }

    /**
     * @param int $id
     *
     * @return User
     */
    private function getGrouphubUser($id)
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        if ($user === null) {
            throw new NotFoundHttpException('User with id: ' . $id . ' not found');
        }

        return $user;
    }
}
