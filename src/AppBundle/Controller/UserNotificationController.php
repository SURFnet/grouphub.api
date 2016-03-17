<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Notification;
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
        $notifications = $this->get('app.manager.notification')->findNotificationsForUser(
            $id,
            $request->query->get('group')
        );

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
        $notification = $this->get('app.manager.notification')->findNotification($notificationId, $userId);

        if ($notification === null) {
            throw new NotFoundHttpException('Notification ' . $notificationId . ' not found for user ' . $userId);
        }

        return $notification;
    }
}
