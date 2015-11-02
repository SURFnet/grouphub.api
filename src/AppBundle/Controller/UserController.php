<?php
namespace AppBundle\Controller;

use AppBundle\Entity\User;
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
 *
 * @package AppBundle\Controller
 */
class UserController extends FOSRestController
{
    /**
     * List all users from the database. Does not support paging!
     *
     * @ApiDoc(
     *  resource = true,
     *  output="ArrayCollection<AppBundle\Entity\User>",
     *  statusCodes = {
     *      200 = "Returned when successul",
     *      500 = "Returned when there is an internal server error"
     *   }
     * )
     *
     * @return array
     */
    public function getUsersAction()
    {
        $list = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll();

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
     * @param Request $request the request object
     * @param int     $id      the User id
     *
     * @return array
     *
     * @throws NotFoundHttpException when user not exist
     */
    public function getUserAction(Request $request, $id)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($id);

        if (!$user) {
            throw new NotFoundHttpException(
                'User with id: ' . $id . ' not found'
            );
        }

        return $this->view([$user]);
    }

    /**
     * Creates a new user from the submitted JSON data.
     *
     * @ApiDoc(
     *   input = "AppBundle\Entity\User",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is invalid"
     *   }
     * )
     *
     * @param Request $request the request object
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

                return $this->routeRedirectView('get_user', array('id' => $user->getId()));
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
    public function putUserAction(Request $request, $id) {

        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($id);

        if (!$user) {
            throw new NotFoundHttpException('User with id: ' . $id . ' not found');
        }

        $form = $this->createForm(new UserType(), $user);
        $form->submit($request);

        if ($form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();

                $em->persist($user);
                $em->flush();

                return $this->routeRedirectView('get_user', array('id' => $user->getId()));
            }
            catch (DBALException $e) {
                throw new NotAcceptableHttpException($e->getMessage());
            }
        }

        return $form->getErrors();
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

     * @param Request $request
     * @param int $id User ID

     * @return array
     *
     * @throws NotFoundHttpException when user not exist
     */
    public function deleteUserAction(Request $request, $id) {

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        if (!$user) {
            throw new NotFoundHttpException('User with id: ' . $id . ' not found');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return $this->routeRedirectView('get_users');
    }
}
