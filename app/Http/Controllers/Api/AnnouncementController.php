<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::query();

        $user = $request->user();
        if (! ($user->isSuperAdmin() || $user->isHRAdmin())) {
            $query->where('is_published', true)
                ->where(function ($q) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', now()->toDateString());
                });
        }

        $announcements = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($announcements);
    }

    public function store(Request $request)
    {
        $this->assertCanManageAnnouncements($request);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'is_active' => 'boolean',
            'is_published' => 'boolean',
            'expiry_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'priority' => $validated['priority'],
            'start_date' => now()->toDateString(),
            'end_date' => $validated['expiry_date'] ?? $validated['end_date'] ?? null,
            'is_published' => $validated['is_active'] ?? $validated['is_published'] ?? true,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($announcement, 201);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->assertCanManageAnnouncements($request);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'priority' => 'sometimes|in:low,medium,high',
            'is_active' => 'boolean',
            'is_published' => 'boolean',
            'expiry_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $data = [];

        foreach (['title', 'content', 'priority'] as $field) {
            if (array_key_exists($field, $validated)) {
                $data[$field] = $validated[$field];
            }
        }

        if (array_key_exists('is_active', $validated) || array_key_exists('is_published', $validated)) {
            $data['is_published'] = $validated['is_active'] ?? $validated['is_published'];
        }

        if (array_key_exists('expiry_date', $validated) || array_key_exists('end_date', $validated)) {
            $data['end_date'] = $validated['expiry_date'] ?? $validated['end_date'] ?? null;
        }

        $announcement->update($data);

        return response()->json($announcement);
    }

    public function destroy(Request $request, Announcement $announcement)
    {
        $this->assertCanManageAnnouncements($request);

        $announcement->delete();

        return response()->json(['message' => 'Announcement deleted successfully']);
    }

    /**
     * Only HR Admin and Super Admin may create, edit, or delete announcements.
     */
    private function assertCanManageAnnouncements(Request $request): void
    {
        $user = $request->user();

        if ($user->isSuperAdmin() || $user->hasRole('hr_admin')) {
            return;
        }

        abort(403, 'Only HR Admin can manage announcements.');
    }
}
