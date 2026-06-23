@extends('layouts.app')

@section('title', 'Dashboard - Task Manager')
@section('header', 'Dashboard')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Total Tasks</h3>
        <p class="text-3xl font-bold text-blue-600">{{ $totalTasks }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Completed</h3>
        <p class="text-3xl font-bold text-green-500">{{ $completedTasks }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Overdue</h3>
        <p class="text-3xl font-bold text-red-500">{{ $overdueTasks }}</p>
    </div>
</div>

<!-- Charts Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Tasks by Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 border-b border-gray-100 pb-3 mb-4">Tasks by Status</h2>
        <div class="relative h-64 w-full">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- Tasks by Priority -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 border-b border-gray-100 pb-3 mb-4">Tasks by Priority</h2>
        <div class="relative h-64 w-full">
            <canvas id="priorityChart"></canvas>
        </div>
    </div>

    <!-- Team Workload -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 border-b border-gray-100 pb-3 mb-4">Team Workload</h2>
        <div class="relative h-64 w-full">
            <canvas id="workloadChart"></canvas>
        </div>
    </div>
</div>

<!-- Recent Tasks -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h2 class="text-lg font-semibold text-gray-800 border-b border-gray-100 pb-3 mb-4">Recent Tasks</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500 border-b border-gray-200">Task Title</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500 border-b border-gray-200">Status</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500 border-b border-gray-200">Priority</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500 border-b border-gray-200">Deadline</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500 border-b border-gray-200">Assignees</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentTasks as $task)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $task->title }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $task->status ? $task->status->name : 'N/A' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $pName = $task->priority ? strtolower($task->priority->name) : '';
                            $pClass = 'bg-gray-100 text-gray-800';
                            if(str_contains($pName, 'high') || str_contains($pName, 'tinggi')) $pClass = 'bg-red-100 text-red-800';
                            elseif(str_contains($pName, 'med') || str_contains($pName, 'sedang')) $pClass = 'bg-yellow-100 text-yellow-800';
                            elseif(str_contains($pName, 'low') || str_contains($pName, 'rendah')) $pClass = 'bg-green-100 text-green-800';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pClass }}">
                            {{ $task->priority ? $task->priority->name : 'N/A' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">
                        {{ $task->deadline_date ? \Carbon\Carbon::parse($task->deadline_date)->format('M d, Y') : '-' }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center -space-x-2">
                            @forelse($task->assignees as $assignee)
                                <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold border-2 border-white" title="{{ $assignee->full_name }}">
                                    {{ strtoupper(substr($assignee->full_name, 0, 1)) }}
                                </div>
                            @empty
                                <span class="text-sm text-gray-400">Unassigned</span>
                            @endforelse
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                        No recent tasks found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const statusData = @json($tasksByStatus);
        const priorityData = @json($tasksByPriority);
        const workloadData = @json($teamWorkload);

        Chart.defaults.font.family = "'Inter', system-ui, sans-serif";

        const getLabels = (dataObj) => Object.keys(dataObj);
        const getValues = (dataObj) => Object.values(dataObj);

        const palette = [
            '#3b82f6', // blue-500
            '#10b981', // emerald-500
            '#f59e0b', // amber-500
            '#ef4444', // red-500
            '#8b5cf6', // violet-500
            '#ec4899'  // pink-500
        ];

        // 1. Status Doughnut Chart
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: getLabels(statusData),
                datasets: [{
                    data: getValues(statusData),
                    backgroundColor: palette,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                cutout: '70%'
            }
        });

        // 2. Priority Bar Chart
        new Chart(document.getElementById('priorityChart'), {
            type: 'bar',
            data: {
                labels: getLabels(priorityData),
                datasets: [{
                    label: 'Tasks',
                    data: getValues(priorityData),
                    backgroundColor: [
                        '#ef4444', // red
                        '#f59e0b', // yellow
                        '#10b981'  // green
                    ],
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 3. Team Workload Bar Chart
        new Chart(document.getElementById('workloadChart'), {
            type: 'bar',
            data: {
                labels: getLabels(workloadData),
                datasets: [{
                    label: 'Assigned Tasks',
                    data: getValues(workloadData),
                    backgroundColor: '#8b5cf6', // violet
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                    y: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endsection
