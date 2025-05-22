<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // عرض إشعارات المستخدم مع Pagination
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 20);
        $userId = auth()->id();

        $notifications = $this->notificationService->getNotifications($userId, $perPage);

        return response()->json($notifications);
    }

    // إرجاع عدد الإشعارات غير المقروءة
    public function unreadCount()
    {
        $userId = auth()->id();
        $count = $this->notificationService->getUnreadNotificationsCount($userId);

        return response()->json(['unread_count' => $count]);
    }

    // تحديث حالة إشعار كمقروء
    public function markAsRead($id)
    {
        $userId = auth()->id();
        $updated = $this->notificationService->markAsRead($id, $userId);

        if ($updated) {
            return response()->json(['message' => 'Notification marked as read']);
        }

        return response()->json(['message' => 'Notification not found or unauthorized'], 404);
    }

    public function destroy($id)
    {
        $userId = auth()->id();
        $deleted = $this->notificationService->deleteNotification($id, $userId);

        if ($deleted) {
            return response()->json(['message' => 'Notification deleted']);
        }
        return response()->json(['message' => 'Notification not found or unauthorized'], 404);
    }

    public function destroyAll()
    {
        $userId = auth()->id();
        $countDeleted = $this->notificationService->deleteAllNotifications($userId);

        return response()->json(['message' => "$countDeleted notifications deleted"]);
    }

}
