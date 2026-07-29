<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card
            title="Active Projects"
            :value="$activeProjects ?? 0"
            subtitle="Currently in production"
            color="indigo"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'
        />

        <x-stat-card
            title="Delayed Projects"
            :value="$delayedProjects ?? 0"
            subtitle="Require attention"
            color="red"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
        />

        <x-stat-card
            title="Low Stock Alerts"
            :value="$lowStockAlerts ?? 0"
            subtitle="Items below minimum"
            color="amber"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'
        />

        <x-stat-card
            title="Upcoming Deadlines"
            :value="$upcomingDeadlines ?? 0"
            subtitle="Within next 7 days"
            color="blue"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'
        />
    </div>

    {{-- Monthly Projects Analytics --}}
    <div class="mt-8">
        <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Monthly Projects Analytics</h3>
                <div id="projects-chart" class="w-full" style="height: 300px;">
                    <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500">
                        <p>Chart loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Upcoming Deadlines --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Upcoming Deadlines</h3>
                    <div class="space-y-4">
                        @forelse (($upcomingProjects ?? collect()) as $project)
                            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $project->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $project->client->name ?? 'N/A' }}</p>
                                </div>
                                @php
                                    $daysLeft = $project->delivery_date ? now()->diffInDays($project->delivery_date, false) : null;
                                @endphp
                                <div class="text-right">
                                    @if($daysLeft !== null)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $daysLeft <= 3 ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' : ($daysLeft <= 7 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300' : 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300') }}">
                                            {{ $daysLeft > 0 ? $daysLeft . ' days left' : ($daysLeft == 0 ? 'Due today' : abs($daysLeft) . ' days overdue') }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">No date set</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No upcoming deadlines</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Projects Table --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Projects</h3>
                        <a href="{{ route('projects.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Order #</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Project</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Client</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">PM</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Team</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Delivery</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse (($recentProjects ?? collect()) as $project)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $project->order_no }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <a href="{{ route('projects.show', $project) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                                {{ $project->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $project->client->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $project->projectManager->full_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            @foreach($project->teams->take(2) as $team)
                                                <span class="inline-block bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs px-2 py-0.5 rounded">{{ $team->name }}</span>
                                            @endforeach
                                            @if($project->teams->count() > 2)
                                                <span class="text-xs text-gray-400">+{{ $project->teams->count() - 2 }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <x-status-badge :status="$project->status" />
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $project->delivery_date?->format('M d, Y') ?? 'N/A' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No projects found. <a href="{{ route('projects.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Create your first project</a>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('projects-chart');
            if (!ctx) return;

            // Replace the placeholder with a canvas
            ctx.innerHTML = '<canvas id="projects-canvas"></canvas>';
            const canvas = document.getElementById('projects-canvas');

            @php
                $defaultChart = ['labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'], 'datasets' => []];
            @endphp
            const chartData = @json($chartData ?: $defaultChart);

            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: chartData.labels ?? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                    datasets: chartData.datasets?.length ? chartData.datasets : [
                        {
                            label: 'Projects Created',
                            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                            backgroundColor: 'rgba(99, 102, 241, 0.5)',
                            borderColor: 'rgb(99, 102, 241)',
                            borderWidth: 1
                        },
                        {
                            label: 'Projects Completed',
                            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                            backgroundColor: 'rgba(16, 185, 129, 0.5)',
                            borderColor: 'rgb(16, 185, 129)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
