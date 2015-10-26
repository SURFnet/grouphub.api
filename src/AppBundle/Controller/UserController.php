<?php
namespace AppBundle\Controller;

use AppBundle\Entity\User;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Rest controller for users.
 *
 * @package AppBundle\Controller
 */
class UserController extends FOSRestController
{
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
    public function postUserAction(Request $request)
    {
        $user = new User();
        $user->setReference(time());

        $form = $this->createFormBuilder($user)
            ->add('firstName', 'text')
            ->add('lastName', 'text')
            ->add('loginName', 'text')
            ->add('timeStamp', 'date')
            ->getForm();
        $form->handleRequest($request);
        return $this->view([$user]);
    }
}
