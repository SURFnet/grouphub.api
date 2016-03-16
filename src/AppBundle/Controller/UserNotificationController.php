<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Notification;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UserNotificationController
 */
class UserNotificationController extends FOSRestController
{
    /**
     * @param int     $id
     * @param Request $request
     *
     * @return View
     */
    public function getUserNotificationsAction($id, Request $request)
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        if ($user === null) {
            throw new NotFoundHttpException('User with id: ' . $id . ' not found');
        }

        $filters = ['to' => $user];

        if ($request->query->has('group')) {
            $filters['group'] = $request->query->getInt('group');
        }

        $notifications = $this->getDoctrine()->getRepository('AppBundle:Notification')->findBy($filters);

        return $this->view($notifications);
    }

    /**
     * @param int $userId
     * @param int $id
     *
     * @return View
     */
    public function deleteUserNotificationsAction($userId, $id)
    {
        $notification = $this->getNotification($userId, $id);

        $this->get('app.manager.notification')->deleteNotification($notification);

        return $this->routeRedirectView('get_user_notifications', ['id' => $userId]);
    }

    /**
     * @param int     $userId
     * @param int     $id
     * @param Request $request
     *
     * @return View
     */
    public function postUserNotificationsResponseAction($userId, $id, Request $request)
    {
        $response = $request->request->get('type');

        if (!in_array($response, ['confirm', 'deny'])) {
            throw new BadRequestHttpException();
        }

        $notification = $this->getNotification($userId, $id);

        $this->get('app.manager.notification')->processNotification($notification, $response);

        return $this->routeRedirectView('get_user_notifications', ['id' => $userId]);
    }

    /**
     * @param int $userId
     * @param int $notificationId
     *
     * @return Notification
     */
    private function getNotification($userId, $notificationId)
    {
        /** @var Notification $notification */
        $notification = $this->getDoctrine()->getRepository('AppBundle:Notification')->findOneBy(
            ['to' => $userId, 'id' => $notificationId]
        );

        if ($notification === null) {
            throw new NotFoundHttpException('Notification ' . $notificationId . ' not found for user ' . $userId);
        }

        return $notification;
    }
}
