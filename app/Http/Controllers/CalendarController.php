<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\Project;
use Illuminate\View\View;

class CalendarController extends Controller
{
    /**
     * Display the calendar view with events.
     */
    public function index(): View
    {
        // Get custom calendar events
        $calendarEvents = CalendarEvent::with('creator')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => 'event-' . $event->id,
                    'title' => $event->title,
                    'start' => $event->start_date->toIso8601String(),
                    'end' => $event->end_date?->toIso8601String(),
                    'color' => $event->color ?? '#6366f1',
                    'all_day' => $event->all_day,
                    'url' => null,
                ];
            });

        // Get project delivery dates as events
        $projectDeliveries = Project::whereNotNull('delivery_date')
            ->whereNotIn('status', ['delivered'])
            ->with('client')
            ->get()
            ->map(function ($project) {
                return [
                    'id' => 'delivery-' . $project->id,
                    'title' => $project->name . ' - Delivery',
                    'start' => $project->delivery_date->toIso8601String(),
                    'end' => null,
                    'color' => match ($project->status) {
                        'delayed' => '#ef4444',
                        'in_production' => '#f59e0b',
                        'inspection' => '#a855f7',
                        'design' => '#0ea5e9',
                        'confirmed' => '#3b82f6',
                        default => '#6b7280',
                    },
                    'all_day' => true,
                    'url' => route('projects.show', $project),
                ];
            });

        // Get project production deadlines as events
        $projectDeadlines = Project::whereNotNull('production_deadline')
            ->whereNotIn('status', ['delivered', 'finished'])
            ->get()
            ->map(function ($project) {
                return [
                    'id' => 'deadline-' . $project->id,
                    'title' => $project->name . ' - Production Deadline',
                    'start' => $project->production_deadline->toIso8601String(),
                    'end' => null,
                    'color' => $project->production_deadline->isPast() ? '#ef4444' : '#10b981',
                    'all_day' => true,
                    'url' => route('projects.show', $project),
                ];
            });

        $events = collect($calendarEvents)
            ->concat($projectDeliveries)
            ->concat($projectDeadlines)
            ->values()
            ->toArray();

        return view('calendar.index', compact('events'));
    }
}
