<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarEventController extends Controller
{
    /**
     * Return a list of calendar events (JSON, filterable by date range).
     */
    public function index(Request $request): JsonResponse
    {
        $query = CalendarEvent::query();

        if ($request->filled('start')) {
            $query->where('start_date', '>=', $request->input('start'));
        }

        if ($request->filled('end')) {
            $query->where('end_date', '<=', $request->input('end'));
        }

        $events = $query->with('creator')->get();

        return response()->json($events);
    }

    /**
     * Store a newly created calendar event.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'max:191'],
            'description' => ['nullable'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'all_day' => ['nullable', 'boolean'],
            'color' => ['nullable', 'max:20'],
            'eventable_type' => ['nullable', 'string'],
            'eventable_id' => ['nullable', 'integer'],
        ]);

        $validated['created_by'] = auth()->id();

        $event = CalendarEvent::create($validated);

        return response()->json($event, 201);
    }

    /**
     * Update the specified calendar event.
     */
    public function update(Request $request, CalendarEvent $calendarEvent): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'max:191'],
            'description' => ['nullable'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'all_day' => ['nullable', 'boolean'],
            'color' => ['nullable', 'max:20'],
        ]);

        $calendarEvent->update($validated);

        return response()->json($calendarEvent);
    }

    /**
     * Remove the specified calendar event.
     */
    public function destroy(CalendarEvent $calendarEvent): JsonResponse
    {
        $calendarEvent->delete();

        return response()->json(['message' => 'Event deleted successfully.']);
    }
}
