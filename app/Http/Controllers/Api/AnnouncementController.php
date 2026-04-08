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

        if (!auth()->user()->role === 'admin') {
            $query->where('is_active', true)
                  ->where(function ($q) {
                      $q->whereNull('expiry_date')
                        ->orWhere('expiry_date', '>=', now());
                  });
        }

        $announcements = $query->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($announcements);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'is_active' => 'boolean',
            'expiry_date' => 'nullable|date',
        ]);

        $validated['created_by'] = auth()->id();

        $announcement = Announcement::create($validated);
        return response()->json($announcement, 201);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'is_active' => 'boolean',
            'expiry_date' => 'nullable|date',
        ]);

        $announcement->update($validated);
        return response()->json($announcement);
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return response()->json(['message' => 'Announcement deleted successfully']);
    }
}
