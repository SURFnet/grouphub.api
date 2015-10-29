<?php
namespace AppBundle\Controller;

use AppBundle\Entity\User;

use AppBundle\Form\UserType;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Rest controller for users.
 *
 * @package AppBundle\Controller
 */
class UserController extends FOSRestController
{
    /**
     * Get list of users
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successul",
     *      500 = "Returned when there is an internal server error"
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @return array
     */
    public function getUsersAction()
    {
        $list = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll();

        if (!$list) {
            throw new HttpException('No users found');
        }

        return $this->view($list);
    }

    /**
     * Get a single user.
     *
     * @ApiDoc(
     *   output = "AppBundle\Entity\User",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="user")
     *
     * @param Request $request the request object
     * @param int     $id      the user id
     *
     * @return array
     *
     * @throws NotFoundHttpException when note not exist
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
     *   output = "AppBundle\Entity\User",
     *   input = "AppBundle\Entity\User",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is invalid"
     *   }
     * )
     *
     * @Annotations\View(templateVar="user")
     *
     * @param Request $request the request object
     *
     * @return array
     */
    public function postUsersAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(new UserType(), $user);
        $form->submit($request);

        if ($form->isValid()) {

            // Created timestamp.
            $user->setTimeStamp(new \DateTime());

            $em = $this->getDoctrine()
                ->getManager();

            $em->persist($user);
            $em->flush();

            return $this->routeRedirectView(
                'get_user',
                array(
                    'id' => $user->getId()
                )
            );
        }
        return $form->getErrors();
    }

    /**
     * Update a single user as a whole. Mind that a PUT requires all user properties included in the JSON object
     *
     * @ApiDoc(
     *   output = "AppBundle\Entity\User",
     *   resource = true,
     *   description="Update a user. Make sure to include all properties!",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="user")
     *
     * @param Request $request the request object
     * @param int     $id      the user id
     *
     * @return array
     *
     * @throws NotFoundHttpException when note not exist
     */
    public function putUserAction(Request $request, $id) {

        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($id);

        if (!$user) {
            throw new NotFoundHttpException(
                'User with id: ' . $id . ' not found'
            );
        }

        $form = $this->createForm(new UserType(), $user);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()
                ->getManager();

            $em->persist($user);
            $em->flush();

            return $this->routeRedirectView(
                'get_user',
                array(
                    'id' => $user->getId()
                )
            );
        }

        return $form->getErrors();
    }
}
