<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Welcome back. Here's your manufacturing overview</p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="relative hidden md:block">
                        <!-- <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div> -->
                        <!-- <input type="text" placeholder="Search" class="block w-64 pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Active Projects -->
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-title">Active Projects</span>
                    <div class="w-3 h-3 rounded-sm bg-gray-900 dark:bg-white"></div>
                </div>
                <div class="stat-card-value">{{ $activeProjects ?? 0 }}</div>
                <div class="stat-card-subtitle">Currently in production</div>
            </div>

            <!-- Delayed Projects -->
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-title">Delayed Projects</span>
                    <div class="w-3 h-3 rounded-full bg-gray-900 dark:bg-white"></div>
                </div>
                <div class="stat-card-value">{{ $delayedProjects ?? 0 }}</div>
                <div class="stat-card-subtitle">Require attention</div>
            </div>

            <!-- Low Stock Alerts -->
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-title">Low Stock Alerts</span>
                    <svg class="w-3 h-3 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 20h20L12 2z"/>
                    </svg>
                </div>
                <div class="stat-card-value">{{ $lowStockAlerts ?? 0 }}</div>
                <div class="stat-card-subtitle">Items below minimum</div>
            </div>

            <!-- Upcoming Deadlines -->
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-title">Upcoming Deadlines</span>
                    <div class="w-3 h-3 rounded-full bg-gray-900 dark:bg-white"></div>
                </div>
                <div class="stat-card-value">{{ $upcomingDeadlines ?? 0 }}</div>
                <div class="stat-card-subtitle">Within next 7 days</div>
            </div>
        </div>

        <!-- Charts and Deadlines Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Projects Analytics Chart -->
            <div class="lg:col-span-2 chart-container">
                <div class="section-header">
                    <h3 class="section-title">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Projects Analytics
                    </h3>
                </div>
                <div class="h-64" id="projectsChart">
                    <canvas id="analyticsChart"></canvas>
                </div>
            </div>

            <!-- Upcoming Deadlines -->
            <div class="chart-container">
                <div class="section-header">
                    <h3 class="section-title">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Upcoming Deadlines
                    </h3>
                    <a href="{{ route('projects.index') }}" class="text-sm text-teal-600 dark:text-teal-400 hover:underline">View All</a>
                </div>
                <div class="space-y-5 mt-2">
                    @forelse(($upcomingProjects ?? collect()) as $project)
                        @php
                            $daysLeft = $project->delivery_date ? (int) now()->diffInDays($project->delivery_date, false) : null;
                        @endphp
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-gray-900 dark:bg-white mt-1.5 flex-shrink-0"></div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $project->name }}</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ $project->client->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                    @if($daysLeft !== null)
                                        @if($daysLeft > 0)
                                            {{ $daysLeft }} days left
                                        @elseif($daysLeft == 0)
                                            Due today
                                        @else
                                            {{ abs($daysLeft) }} days overdue
                                        @endif
                                    @else
                                        No date set
                                    @endif
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No upcoming deadlines</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Projects Table -->
        <div class="chart-container">
            <div class="section-header">
                <h3 class="section-title">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    Recent Projects
                </h3>
                <a href="{{ route('projects.index') }}" class="text-sm text-teal-600 dark:text-teal-400 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order No</th>
                            <th>Delivery Date</th>
                            <th>Project</th>
                            <th>Project Manager</th>
                            <th>Client</th>
                            <th>Team</th>
                            <th>Status</th>
                            <th>Production Deadline</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($recentProjects ?? collect()) as $project)
                            <tr>
                                <td class="font-medium">{{ $project->order_no }}</td>
                                <td>{{ $project->delivery_date?->format('d M, Y') ?? 'N/A' }}</td>
                                <td>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $project->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $project->description ? Str::limit($project->description, 30) : '' }}</div>
                                    </div>
                                </td>
                                <td>{{ $project->projectManager->full_name ?? 'N/A' }}</td>
                                <td>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $project->client->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $project->client->contact_person ?? '' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-xs">
                                        @if($project->members && $project->members->count() > 0)
                                            @foreach($project->members->take(3) as $member)
                                                <span class="text-gray-{{ $loop->first ? '900 dark:text-white' : '500 dark:text-gray-400' }}">{{ $member->first_name }}{{ !$loop->last ? ',' : '' }}</span>
                                            @endforeach
                                            @if($project->members->count() > 3)
                                                <span class="text-gray-400">+{{ $project->members->count() - 3 }}</span>
                                            @endif
                                        @elseif($project->teams && $project->teams->count() > 0)
                                            @foreach($project->teams->take(2) as $team)
                                                <span class="text-gray-{{ $loop->first ? '900 dark:text-white' : '500 dark:text-gray-400' }}">{{ $team->name }}{{ !$loop->last ? ',' : '' }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($project->status) {
                                            'in_production' => 'badge-production',
                                            'confirmed' => 'badge-progress',
                                            'delayed' => 'badge-progress',
                                            'finished', 'delivered' => 'badge-completed',
                                            default => 'badge-progress',
                                        };
                                        $statusLabel = ucfirst(str_replace('_', ' ', $project->status));
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                                </td>
                                <td>{{ $project->production_deadline?->format('d-m-Y') ?? 'N/A' }}</td>
                                <td>
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" @click.away="open = false" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                            </svg>
                                        </button>
                                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-44 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10" style="display: none;">
                                            <a href="{{ route('projects.show', $project) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">View</a>
                                            <a href="{{ route('projects.edit', $project) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Edit</a>
                                            <x-confirm-delete
                                                :action="route('projects.destroy', $project)"
                                                message="Are you sure you want to delete this project? All associated data will be permanently removed."
                                                title="Delete Project"
                                                buttonClass="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                Delete
                                            </x-confirm-delete>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    No projects found. <a href="{{ route('projects.create') }}" class="text-teal-600 dark:text-teal-400 hover:underline">Create your first project</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Served locally instead of from a CDN - see calendar/index.blade.php for why. --}}
    <script src="{{ asset('vendor/chartjs/chart.umd.js') }}"></script>
    <script>
        function initDashboardChart() {
            const ctx = document.getElementById('analyticsChart');
            if (ctx) {
                const existingChart = typeof Chart !== 'undefined' && Chart.getChart(ctx);
                if (existingChart) {
                    existingChart.destroy();
                }

                const isDark = document.documentElement.classList.contains('dark') ||
                               localStorage.getItem('darkMode') === 'true';

                const chartData = @json($chartData ?? ['labels' => [], 'data' => []]);

                const chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Projects',
                            data: chartData.data || [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                            borderColor: isDark ? '#9ca3af' : '#374151',
                            backgroundColor: 'transparent',
                            borderWidth: 1.5,
                            tension: 0.1,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: isDark ? '#1f2937' : '#ffffff',
                            pointHoverBorderColor: isDark ? '#9ca3af' : '#374151',
                            pointHoverBorderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1f2937',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: 'transparent',
                                borderWidth: 0,
                                padding: { top: 4, bottom: 4, left: 8, right: 8 },
                                cornerRadius: 4,
                                displayColors: false,
                                titleFont: { size: 0 },
                                bodyFont: { size: 12, weight: 'bold' },
                                callbacks: {
                                    title: function() { return ''; },
                                    label: function(context) {
                                        return context.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: isDark ? '#6b7280' : '#9ca3af',
                                    font: { size: 11 }
                                },
                                grid: {
                                    color: isDark ? '#374151' : '#f3f4f6',
                                    drawBorder: false
                                },
                                border: { display: false }
                            },
                            x: {
                                ticks: {
                                    color: isDark ? '#6b7280' : '#9ca3af',
                                    font: { size: 11 }
                                },
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                border: { display: false }
                            }
                        }
                    }
                });
            }
        }

        // See calendar/index.blade.php for why this checks readyState instead
        // of only listening for DOMContentLoaded - by the time a script at
        // the end of the page runs, that event has usually already fired.
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDashboardChart);
        } else {
            initDashboardChart();
        }
        document.addEventListener('livewire:navigated', initDashboardChart);
    </script>
    @endpush
</x-app-layout>
