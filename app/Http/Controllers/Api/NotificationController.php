<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        $query = Notification::where('user_id', $request->user()->id);

        if ($request->has('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $notifications = $query->latest()->paginate(20);
        return response()->json($notifications);
    }

    public function getUnreadCount(Request $request)
    {
        $count = Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json($notification);
    }

    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function deleteNotification($id)
    {
        $notification = Notification::where('user_id', auth()->id())
            ->findOrFail($id);

        $notification->delete();

        return response()->json(['message' => 'Notification deleted successfully']);
    }

    public function clearAll(Request $request)
    {
        Notification::where('user_id', $request->user()->id)->delete();

        return response()->json(['message' => 'All notifications cleared']);
    }

    // Notification Preferences
    public function getPreferences(Request $request)
    {
        $preferences = NotificationPreference::where('user_id', $request->user()->id)->get();
        return response()->json($preferences);
    }

    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'notification_type' => 'required|in:email,push,in_app',
            'enabled_events' => 'required|array',
        ]);

        $preference = NotificationPreference::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'notification_type' => $validated['notification_type'],
            ],
            [
                'enabled_events' => $validated['enabled_events'],
            ]
        );

        return response()->json($preference);
    }

    // Helper method to create notifications
    public static function create($userId, $type, $title, $message, $data = null, $actionUrl = null, $priority = 'normal')
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => $actionUrl,
            'priority' => $priority,
        ]);
    }
}
