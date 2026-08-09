<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Calendar</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Project deadlines, deliveries, and scheduled events</p>
            </div>

            <!-- Color Legend -->
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 mt-4">
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: #3b82f6"></span> Confirmed
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: #0ea5e9"></span> Design
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: #f59e0b"></span> In Production
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: #a855f7"></span> Inspection
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: #ef4444"></span> Delayed / Overdue
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: #10b981"></span> Deadline (upcoming)
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: #6366f1"></span> Custom Event
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: #6b7280"></span> Other
                </span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6">
            <div id="calendar"></div>
        </div>
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <style>
        /* Dark mode overrides for FullCalendar */
        .dark .fc {
            --fc-border-color: #1f2937;
            --fc-button-bg-color: #374151;
            --fc-button-border-color: #374151;
            --fc-button-hover-bg-color: #4b5563;
            --fc-button-hover-border-color: #4b5563;
            --fc-button-active-bg-color: #6b7280;
            --fc-button-active-border-color: #6b7280;
            --fc-event-bg-color: #4f46e5;
            --fc-event-border-color: #4f46e5;
            --fc-today-bg-color: rgba(99, 102, 241, 0.1);
            --fc-page-bg-color: #111827;
            --fc-neutral-bg-color: #1f2937;
            --fc-list-event-hover-bg-color: #1f2937;
        }
        .dark .fc .fc-col-header-cell-cushion,
        .dark .fc .fc-daygrid-day-number,
        .dark .fc .fc-list-day-text,
        .dark .fc .fc-list-day-side-text {
            color: #d1d5db;
        }
        .dark .fc .fc-list-event td {
            border-color: #1f2937;
        }
        .dark .fc .fc-toolbar-title {
            color: #f9fafb;
        }
        .dark .fc .fc-button {
            border-radius: 0.375rem;
        }
        /* Light mode cleanup */
        .fc .fc-button {
            border-radius: 0.375rem;
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }
        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
    </style>
    @endpush

    @push('scripts')
    <!-- <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script> -->
     <script src="{{ asset('vendor/fullcalendar/index.global.min.js') }}"></script>
    <script>
        function initCalendar() {
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl || calendarEl.dataset.calendarInitialized) return;
             if (typeof FullCalendar === 'undefined') {
                setTimeout(initCalendar, 50);
                return;
            }
            calendarEl.dataset.calendarInitialized = '1';

            const events = @json($events ?? []);

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                events: events.map(event => ({
                    id: event.id,
                    title: event.title,
                    start: event.start_date || event.start,
                    end: event.end_date || event.end,
                    url: event.url || null,
                    color: event.color || null,
                    allDay: event.all_day ?? true,
                })),
                eventClick: function (info) {
                    if (info.event.url) {
                        info.jsEvent.preventDefault();
                        window.location.href = info.event.url;
                    }
                },
                height: 'auto',
                navLinks: true,
                editable: false,
                dayMaxEvents: true,
            });

            calendar.render();
        }

        // document.addEventListener('DOMContentLoaded', initCalendar);
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCalendar);
        } else {
            initCalendar();
        }
        document.addEventListener('livewire:navigated', initCalendar);
    </script>
    @endpush
</x-app-layout>
