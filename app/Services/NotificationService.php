<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Creates in-app notifications and mirrors them as Firebase push
 * notifications to the users' registered devices.
 */
class NotificationService
{
    public function __construct(protected FirebasePushService $push)
    {
    }

    /**
     * Notify a single user (in-app + push). Never throws.
     */
    public function notifyUser(
        ?int $userId,
        string $type,
        string $title,
        string $message,
        array $data = [],
        ?string $actionUrl = null,
        string $priority = 'normal'
    ): void {
        if (!$userId) {
            return;
        }

        $this->notifyUsers([$userId], $type, $title, $message, $data, $actionUrl, $priority);
    }

    /**
     * Notify multiple users (in-app + push). Never throws.
     *
     * @param array<int> $userIds
     */
    public function notifyUsers(
        array $userIds,
        string $type,
        string $title,
        string $message,
        array $data = [],
        ?string $actionUrl = null,
        string $priority = 'normal'
    ): void {
        $userIds = array_values(array_unique(array_filter(array_map('intval', $userIds))));
        if (empty($userIds)) {
            return;
        }

        try {
            $now = now();
            $rows = array_map(fn ($userId) => [
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => json_encode($data),
                'action_url' => $actionUrl,
                'priority' => $priority,
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ], $userIds);

            Notification::insert($rows);
        } catch (\Throwable $e) {
            Log::warning('Failed to create in-app notifications: ' . $e->getMessage(), ['type' => $type]);
        }

        try {
            $this->push->sendToUsers($userIds, $title, $message, array_merge($data, [
                'type' => $type,
                'action_url' => (string) $actionUrl,
                'priority' => $priority,
            ]));
        } catch (\Throwable $e) {
            Log::warning('Failed to send push notifications: ' . $e->getMessage(), ['type' => $type]);
        }
    }

    /**
     * Notify all active users holding any of the given roles.
     *
     * @param array<string> $roles e.g. ['hr_admin', 'super_admin']
     * @param array<int> $excludeUserIds usually the acting user
     */
    public function notifyRoles(
        array $roles,
        string $type,
        string $title,
        string $message,
        array $data = [],
        ?string $actionUrl = null,
        string $priority = 'normal',
        array $excludeUserIds = []
    ): void {
        $userIds = User::whereIn('role', $roles)
            ->where('is_active', true)
            ->whereNotIn('id', array_filter($excludeUserIds))
            ->pluck('id')
            ->all();

        $this->notifyUsers($userIds, $type, $title, $message, $data, $actionUrl, $priority);
    }

    /**
     * Notify every active user (e.g. company-wide announcements).
     *
     * @param array<int> $excludeUserIds
     */
    public function notifyAllUsers(
        string $type,
        string $title,
        string $message,
        array $data = [],
        ?string $actionUrl = null,
        string $priority = 'normal',
        array $excludeUserIds = []
    ): void {
        $userIds = User::where('is_active', true)
            ->whereNotIn('id', array_filter($excludeUserIds))
            ->pluck('id')
            ->all();

        $this->notifyUsers($userIds, $type, $title, $message, $data, $actionUrl, $priority);
    }
}
