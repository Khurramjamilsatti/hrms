<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\Employee;
use App\Models\EventAttendee;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CalendarController extends Controller
{
    public function getEvents(Request $request)
    {
        $user = $request->user();
        $query = CalendarEvent::with(['creator', 'attendees.employee']);

        // Role-based filtering
        if ($user->hasRole('employee')) {
            // Employee can only see their own events and company-wide events
            if ($user->employee) {
                $query->where(function($q) use ($user) {
                    $q->whereHas('attendees', function($attendeeQuery) use ($user) {
                        $attendeeQuery->where('employee_id', $user->employee->id);
                    })->orWhere('event_type', 'company_event')
                      ->orWhere('event_type', 'holiday');
                });
            }
        } elseif ($user->hasRole('manager')) {
            // Manager can see their own events, team events, and company-wide events
            if ($user->employee) {
                $query->where(function($q) use ($user) {
                    $q->whereHas('attendees', function($attendeeQuery) use ($user) {
                        $attendeeQuery->where('employee_id', $user->employee->id)
                            ->orWhereHas('employee', function($empQuery) use ($user) {
                                $empQuery->where('manager_id', $user->id);
                            });
                    })->orWhere('event_type', 'company_event')
                      ->orWhere('event_type', 'holiday');
                });
            }
        } elseif ($user->hasRole('section_head')) {
            // Section head can see department events and company-wide events
            if ($user->employee && $user->employee->department_id) {
                $query->where(function($q) use ($user) {
                    $q->whereHas('attendees', function($attendeeQuery) use ($user) {
                        $attendeeQuery->whereHas('employee', function($empQuery) use ($user) {
                            $empQuery->where('department_id', $user->employee->department_id);
                        });
                    })->orWhere('event_type', 'company_event')
                      ->orWhere('event_type', 'holiday');
                });
            }
        }
        // hr_admin, super_admin, and admin can see all events

        // Filter by date range (inclusive full days)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();

            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_datetime', [$start, $end])
                    ->orWhereBetween('end_datetime', [$start, $end])
                    ->orWhere(function ($inner) use ($start, $end) {
                        $inner->where('start_datetime', '<=', $start)
                            ->where('end_datetime', '>=', $end);
                    });
            });
        }

        // Filter by event type
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        // Filter by current user's events
        if ($request->boolean('my_events') && $user->employee) {
            $employeeId = $user->employee->id;
            $query->whereHas('attendees', function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            });
        }

        $query->orderBy('start_datetime');

        // Calendar month fetch should return the full range, not a short page
        if ($request->boolean('calendar') || ($request->filled('start_date') && $request->filled('end_date'))) {
            $events = $query->get();

            $includeBirthdays = !$request->filled('event_type') || $request->event_type === 'birthday';
            if ($includeBirthdays && !$request->boolean('my_events')) {
                $rangeStart = $request->filled('start_date')
                    ? Carbon::parse($request->start_date)->startOfDay()
                    : now()->startOfMonth();
                $rangeEnd = $request->filled('end_date')
                    ? Carbon::parse($request->end_date)->endOfDay()
                    : now()->endOfMonth();

                $events = $events
                    ->concat($this->birthdayEventsForRange($rangeStart, $rangeEnd))
                    ->sortBy('start_datetime')
                    ->values();
            }

            return response()->json([
                'data' => $events,
            ]);
        }

        $events = $query->paginate($request->integer('per_page', 50));
        return response()->json($events);
    }

    /**
     * Build read-only birthday events for active employees within a date range.
     * Matches month/day across years (Feb 29 -> Feb 28 on non-leap years).
     */
    protected function birthdayEventsForRange(Carbon $rangeStart, Carbon $rangeEnd): Collection
    {
        $employees = Employee::query()
            ->whereNotNull('date_of_birth')
            ->where('employment_status', 'active')
            ->select(['id', 'first_name', 'last_name', 'employee_code', 'date_of_birth', 'department_id'])
            ->with('department:id,name')
            ->get();

        $events = collect();
        $periodYears = range($rangeStart->year, $rangeEnd->year);

        foreach ($employees as $employee) {
            $dob = Carbon::parse($employee->date_of_birth);

            foreach ($periodYears as $year) {
                $month = $dob->month;
                $day = $dob->day;

                if ($month === 2 && $day === 29 && !Carbon::create($year, 1, 1)->isLeapYear()) {
                    $day = 28;
                }

                try {
                    $birthday = Carbon::create($year, $month, $day)->startOfDay();
                } catch (\Throwable $e) {
                    continue;
                }

                if ($birthday->lt($rangeStart->copy()->startOfDay()) || $birthday->gt($rangeEnd->copy()->endOfDay())) {
                    continue;
                }

                $name = trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? ''));
                $age = (int) $dob->diffInYears($birthday);

                $events->push([
                    'id' => 'birthday-' . $employee->id . '-' . $birthday->format('Y-m-d'),
                    'title' => $name . "'s Birthday",
                    'description' => $age > 0
                        ? "{$name} turns {$age}" . ($employee->department?->name ? " · {$employee->department->name}" : '')
                        : ($employee->department?->name ?: 'Employee birthday'),
                    'event_type' => 'birthday',
                    'start_datetime' => $birthday->toIso8601String(),
                    'end_datetime' => $birthday->copy()->endOfDay()->toIso8601String(),
                    'location' => null,
                    'meeting_link' => null,
                    'is_all_day' => true,
                    'is_recurring' => true,
                    'recurrence_rule' => 'YEARLY',
                    'created_by' => null,
                    'is_system' => true,
                    'readonly' => true,
                    'employee_id' => $employee->id,
                    'attendees' => [],
                    'creator' => null,
                ]);
            }
        }

        return $events->sortBy('start_datetime')->values();
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|in:meeting,training,interview,holiday,company_event,other',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url',
            'is_all_day' => 'nullable|boolean',
            'is_recurring' => 'nullable|boolean',
            'recurrence_rule' => 'nullable|string',
            'attendees' => 'nullable|array',
            'attendees.*' => 'exists:employees,id',
        ]);

        $event = CalendarEvent::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'event_type' => $validated['event_type'],
            'start_datetime' => $validated['start_datetime'],
            'end_datetime' => $validated['end_datetime'],
            'location' => $validated['location'] ?? null,
            'meeting_link' => $validated['meeting_link'] ?? null,
            'is_all_day' => $validated['is_all_day'] ?? false,
            'is_recurring' => $validated['is_recurring'] ?? false,
            'recurrence_rule' => $validated['recurrence_rule'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        // Add attendees
        if (isset($validated['attendees'])) {
            foreach ($validated['attendees'] as $employeeId) {
                EventAttendee::create([
                    'event_id' => $event->id,
                    'employee_id' => $employeeId,
                    'status' => 'invited',
                    'is_organizer' => false,
                ]);
            }
        }

        // Add creator as organizer
        if ($request->user()->employee) {
            EventAttendee::create([
                'event_id' => $event->id,
                'employee_id' => $request->user()->employee->id,
                'status' => 'accepted',
                'is_organizer' => true,
            ]);
        }

        return response()->json($event->load(['creator', 'attendees.employee']), 201);
    }

    public function getEvent($id)
    {
        $event = CalendarEvent::with(['creator', 'attendees.employee', 'reminders'])
            ->findOrFail($id);
        return response()->json($event);
    }

    public function updateEvent(Request $request, $id)
    {
        $event = CalendarEvent::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'sometimes|in:meeting,training,interview,holiday,company_event,other',
            'start_datetime' => 'sometimes|date',
            'end_datetime' => 'sometimes|date',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url',
            'is_all_day' => 'nullable|boolean',
        ]);

        $event->update($validated);
        return response()->json($event->load(['creator', 'attendees.employee']));
    }

    public function deleteEvent($id)
    {
        $event = CalendarEvent::findOrFail($id);
        $event->delete();
        return response()->json(['message' => 'Event deleted successfully']);
    }

    // Attendee Response
    public function respondToEvent(Request $request, $eventId)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,declined,tentative,maybe',
            'response_note' => 'nullable|string',
        ]);

        $status = $validated['status'] === 'maybe' ? 'tentative' : $validated['status'];

        if (!$request->user()->employee) {
            return response()->json(['message' => 'Employee profile required to respond'], 422);
        }

        $attendee = EventAttendee::where('event_id', $eventId)
            ->where('employee_id', $request->user()->employee->id)
            ->firstOrFail();

        $attendee->update([
            'status' => $status,
            'response_note' => $validated['response_note'] ?? null,
            'responded_at' => now(),
        ]);

        return response()->json($attendee->load('event'));
    }

    public function addAttendees(Request $request, $eventId)
    {
        $validated = $request->validate([
            'attendees' => 'required|array',
            'attendees.*' => 'exists:employees,id',
        ]);

        $event = CalendarEvent::findOrFail($eventId);
        $added = [];

        foreach ($validated['attendees'] as $employeeId) {
            // Check if not already an attendee
            $existing = EventAttendee::where('event_id', $eventId)
                ->where('employee_id', $employeeId)
                ->first();

            if (!$existing) {
                $added[] = EventAttendee::create([
                    'event_id' => $eventId,
                    'employee_id' => $employeeId,
                    'status' => 'invited',
                    'is_organizer' => false,
                ]);
            }
        }

        return response()->json($added, 201);
    }

    public function removeAttendee($eventId, $attendeeId)
    {
        $attendee = EventAttendee::where('event_id', $eventId)
            ->where('id', $attendeeId)
            ->firstOrFail();

        $attendee->delete();
        return response()->json(['message' => 'Attendee removed successfully']);
    }

    // Reminders
    public function addReminder(Request $request, $eventId)
    {
        if (!$request->user()->employee) {
            return response()->json(['message' => 'Employee profile required to set reminders'], 422);
        }

        $validated = $request->validate([
            'remind_before_minutes' => 'required|integer|min:5',
        ]);

        CalendarEvent::findOrFail($eventId);

        $reminder = Reminder::create([
            'event_id' => $eventId,
            'employee_id' => $request->user()->employee->id,
            'remind_before_minutes' => $validated['remind_before_minutes'],
            'is_sent' => false,
        ]);

        return response()->json($reminder, 201);
    }

    public function getMyEvents(Request $request)
    {
        if (!$request->user()->employee) {
            return response()->json([]);
        }

        $employeeId = $request->user()->employee->id;

        $events = CalendarEvent::whereHas('attendees', function ($q) use ($employeeId) {
            $q->where('employee_id', $employeeId);
        })
        ->with(['creator', 'attendees' => function ($q) use ($employeeId) {
            $q->where('employee_id', $employeeId);
        }])
        ->whereBetween('start_datetime', [
            $request->get('start_date', now()->startOfMonth()),
            $request->get('end_date', now()->endOfMonth()),
        ])
        ->orderBy('start_datetime')
        ->get();

        return response()->json($events);
    }
}
