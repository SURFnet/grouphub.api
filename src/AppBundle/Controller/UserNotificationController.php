<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Notification;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UserNotificationController
 */
class UserNotificationController extends FOSRestController
{
    /**
     * List all notifications of a user.
     *
     * @ApiDoc(
     *  resource = true,
     *  parameters = {
     *      {"name"="group", "dataType"="integer", "required"=false, "description"="group id filter"}
     *  },
     *  requirements = {
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "UserID"
     *     }
     *  },
     *  output="array<AppBundle\Entity\Notification>",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      500 = "Returned when there is an internal server error"
     *   }
     * )
     *
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
     * Remove a notification.
     *
     * @ApiDoc(
     *  resource = true,
     *  requirements = {
     *      {
     *          "name" = "userId",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "UserID"
     *      },
     *      {
     *          "name" = "notificationId",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "NotificationID"
     *      }
     *  },
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the group or user is not found.",
     *      500 = "Returned when there is a internal error."
     *  }
     * )
     *
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
     * Add a response to a notification.
     *
     * @ApiDoc(
     *  resource = true,
     *  requirements = {
     *      {
     *          "name" = "userId",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "UserID"
     *      },
     *      {
     *          "name" = "notificationId",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "NotificationID"
     *      }
     *  },
     *  parameters = {
     *      {"name"="type", "dataType"="string", "required"=true, "description"="response, either 'confirm' or 'deny'"}
     *  },
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the group or user is not found.",
     *      500 = "Returned when there is a internal error."
     *  }
     * )
     *
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
     * Retrieve a single Notification for a User.
     *
     * @ApiDoc(
     *   output = "AppBundle\Entity\Notification",
     *   resource = true,
     *   requirements = {
     *      {
     *          "name" = "userId",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "UserID"
     *      },
     *      {
     *          "name" = "notificationId",
     *          "dataType" = "integer",
     *          "requirement" = "\d+",
     *          "description" = "NotificationID"
     *      }
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the notification is not found",
     *     500 = "Returned when there is a internal error."
     *   }
     * )
     *
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
