<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UserNotificationController
 */
class UserNotificationController extends FOSRestController
{
    /**
     * @param int $id
     *
     * @return array
     */
    public function getUserNotificationsAction($id)
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        if ($user === null) {
            throw new NotFoundHttpException('User with id: '.$id.' not found');
        }

        $notifications = $this->getDoctrine()->getRepository('AppBundle:Notification')->findBy(['to' => $user]);

        return $this->view($notifications);
    }
}
