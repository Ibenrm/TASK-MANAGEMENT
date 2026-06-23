@extends('layouts.app')

@section('title', 'Tugas - Task Manager')
@section('header', 'Tugas')

@push('styles')
<style>
    /* Theme overrides for Kanban Board */
    .kanban-card {
        transition: all 0.2s ease;
        border: 1px solid #e2e8f0;
    }
    .kanban-card:hover {
        border-color: #818cf8; /* Indigo 400 */
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.1), 0 4px 6px -2px rgba(99, 102, 241, 0.05);
        transform: translateY(-2px);
    }
    .btn-indigo {
        background-color: #6366f1;
        color: white;
    }
    .btn-indigo:hover {
        background-color: #4f46e5;
    }
    .avatar-indigo {
        background-color: #6366f1;
        color: white;
        border-color: white;
    }
    
    /* Custom Badges to match premium theme */
    .badge-urgent { background-color: #ffe4e6; color: #e11d48; }
    .badge-normal { background-color: #e0e7ff; color: #4f46e5; } /* Indigo theme for medium */
    .badge-low { background-color: #d1fae5; color: #059669; }
    
    /* Hide empty card if there are tasks in the column */
    .sortable-list:has(.kanban-card) .empty-card {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="h-full flex flex-col min-h-0" x-data="{ showModal: false }">
    <!-- Header/Actions -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100 transition-colors duration-200">Kanban Board</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Kelola tugas Anda berdasarkan status penyelesaian.</p>
        </div>
        <button @click="showModal = true; $dispatch('modal-opened', { task: null })" class="px-4 py-2 text-sm font-medium rounded-lg transition shadow-sm flex items-center gap-2 btn-indigo">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tugas Baru
        </button>
    </div>

    <!-- Kanban Grid -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-6 items-start min-h-[600px]">
        @forelse($statusNodes as $status)
        <!-- Column -->
        <div class="rounded-xl flex flex-col h-full max-h-[80vh] overflow-hidden shadow-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 transition-colors duration-200">
            <!-- Column Header -->
            <div class="p-4 border-b border-gray-200 dark:border-slate-800 flex justify-between items-center bg-slate-100 dark:bg-slate-800/80 transition-colors duration-200">
                <h3 class="font-semibold text-gray-700 dark:text-slate-200 flex items-center gap-2">
                    <!-- Dynamic Dot Color based on Status -->
                    @php
                        $dotColor = 'background-color: #94a3b8;'; // default gray
                        if($status->slug == 'to_do') $dotColor = 'background-color: #cbd5e1;'; // Slate 300
                        if($status->slug == 'in_progress') $dotColor = 'background-color: #6366f1;'; // Indigo 500
                        if($status->slug == 'done') $dotColor = 'background-color: #10b981;'; // Emerald 500
                    @endphp
                    <span class="w-2.5 h-2.5 rounded-full" style="{{ $dotColor }}"></span>
                    <span>{{ $status->name }}</span>
                </h3>
                <span class="column-task-count bg-white dark:bg-slate-700 text-xs font-bold px-2.5 py-1 rounded-full shadow-sm border border-gray-200 dark:border-slate-600 text-slate-500 dark:text-slate-300 transition-colors duration-200">
                    {{ $status->tasks->count() }}
                </span>
            </div>

            <!-- Task List -->
            <div class="p-3 flex-1 overflow-y-auto space-y-3 sortable-list" data-status-id="{{ $status->id }}">
                @foreach($status->tasks as $task)
                <!-- Task Card -->
                <div data-id="{{ $task->id }}" data-task="{{ $task->toJson() }}" @click="showModal = true; $dispatch('modal-opened', { task: JSON.parse($el.dataset.task) })" class="bg-white dark:bg-slate-800 p-3.5 rounded-xl shadow-sm hover:shadow-md cursor-pointer group kanban-card transition-all duration-200 border border-slate-200 dark:border-slate-700 relative flex flex-col gap-3">
                    
                    <!-- Top Row: Badges & Options -->
                    <div class="flex justify-between items-start">
                        <div class="flex flex-wrap gap-1.5">
                            <!-- Priority Badge -->
                            @php
                                $pName = $task->priority ? strtolower($task->priority->name) : '';
                                $pClass = 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-700 dark:text-slate-300';
                                if(str_contains($pName, 'high') || str_contains($pName, 'tinggi')) $pClass = 'bg-red-50 text-red-600 border-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20';
                                elseif(str_contains($pName, 'med') || str_contains($pName, 'sedang')) $pClass = 'bg-indigo-50 text-indigo-600 border-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20';
                                elseif(str_contains($pName, 'low') || str_contains($pName, 'rendah')) $pClass = 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide border {{ $pClass }}">
                                {{ $task->priority ? $task->priority->name : 'No Priority' }}
                            </span>
                            
                            <!-- Status Badge -->
                            <span class="task-status-badge inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-slate-50 text-slate-500 border border-slate-200 dark:bg-slate-700/50 dark:text-slate-400 dark:border-slate-600">
                                {{ $status->name }}
                            </span>
                        </div>
                        
                        <button class="text-slate-400 hover:text-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity p-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                        </button>
                    </div>

                    <!-- Title & Note -->
                    <div>
                        <h4 class="font-bold text-[15px] leading-tight text-slate-800 dark:text-slate-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $task->title }}</h4>
                        @if($task->note)
                        <p class="text-[13px] text-slate-600 dark:text-slate-400 line-clamp-2 mt-1.5 leading-relaxed">{{ $task->note }}</p>
                        @endif
                    </div>

                    <!-- Dates Block -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 p-2.5 rounded-lg border border-slate-200 dark:border-slate-700/60 flex flex-col gap-1.5 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 font-medium dark:text-slate-400">Mulai:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">
                                {{ $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('d M Y') : '-' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 font-medium dark:text-slate-400">Tenggat:</span>
                            @php
                                $isOverdue = $task->deadline_date && \Carbon\Carbon::parse($task->deadline_date)->isPast() && $status->slug != 'done';
                            @endphp
                            <span class="font-bold {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-slate-800 dark:text-slate-200' }}">
                                {{ $task->deadline_date ? \Carbon\Carbon::parse($task->deadline_date)->format('d M Y') : '-' }}
                            </span>
                        </div>
                    </div>

                    @php
                        $totalTodos = $task->todos->count();
                        $completedTodos = $task->todos->where('is_checked', true)->count();
                        $totalComments = $task->comments->count();
                        $hasIndicators = $totalTodos > 0 || $totalComments > 0;
                    @endphp

                    <!-- Footer: Indicators & Assignees -->
                    <div class="flex items-end justify-between mt-1">
                        
                        <!-- Indicators -->
                        <div class="flex flex-col gap-2 w-1/2">
                            @if($hasIndicators)
                            <div class="flex items-center gap-3 text-[13px] font-semibold text-slate-600 dark:text-slate-400">
                                @if($totalTodos > 0)
                                <div class="flex items-center gap-1.5 {{ $completedTodos === $totalTodos ? 'text-emerald-600 dark:text-emerald-400' : '' }}" title="To-Do List">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                    <span>{{ $completedTodos }}/{{ $totalTodos }}</span>
                                </div>
                                @endif
                                
                                @if($totalComments > 0)
                                <div class="flex items-center gap-1.5" title="Komentar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    <span>{{ $totalComments }}</span>
                                </div>
                                @endif
                            </div>
                            @endif

                            @if($totalTodos > 0)
                            <!-- Progress Bar -->
                            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ ($completedTodos / $totalTodos) * 100 }}%"></div>
                            </div>
                            @endif
                        </div>

                        <!-- Assignees -->
                        <div class="flex items-center -space-x-1.5 shrink-0">
                            @php
                                $maxAssignees = 3;
                                $assigneesCount = $task->assignees->count();
                                $visibleAssignees = $task->assignees->take($maxAssignees);
                            @endphp
                            
                            @forelse($visibleAssignees as $assignee)
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[9px] font-bold text-white border-2 border-white dark:border-slate-800 shadow-sm ring-1 ring-black/5" 
                                     style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);"
                                     title="{{ $assignee->full_name }}">
                                    {{ strtoupper(substr($assignee->full_name, 0, 1)) }}
                                </div>
                            @empty
                                <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-700 border-2 border-white dark:border-slate-800 shadow-sm ring-1 ring-black/5 flex items-center justify-center" title="Belum ditugaskan">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                            @endforelse
                            
                            @if($assigneesCount > $maxAssignees)
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[9px] font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 border-2 border-white dark:border-slate-800 shadow-sm ring-1 ring-black/5 z-10" title="+{{ $assigneesCount - $maxAssignees }} lainnya">
                                    +{{ $assigneesCount - $maxAssignees }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="empty-card text-center p-6 text-sm text-gray-400 border-2 border-dashed border-gray-200 rounded-lg bg-gray-50/50 transition-colors duration-200">
                    Tidak ada tugas
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 p-4 rounded-lg flex items-center gap-3 border border-indigo-200 dark:border-indigo-800/50 transition-colors duration-200">
            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Status belum di-seed. Silakan jalankan seeder di terminal.
        </div>
        @endforelse
    </div>
    
    <!-- Modal -->
    @include('partials.task-modal')
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const lists = document.querySelectorAll('.sortable-list');
    lists.forEach(list => {
        new Sortable(list, {
            group: 'shared', // set both lists to same group
            animation: 150,
            ghostClass: 'opacity-50', // Class name for the drop placeholder
            onEnd: function (evt) {
                const itemEl = evt.item;  // dragged HTMLElement
                const newStatusId = evt.to.getAttribute('data-status-id'); // new list
                
                const taskId = itemEl.getAttribute('data-id');
                
                // Get the element before the dropped element
                const prevEl = itemEl.previousElementSibling;
                const prevTaskId = (prevEl && prevEl.getAttribute('data-id')) ? prevEl.getAttribute('data-id') : null;
                
                // Get the element after the dropped element
                const nextEl = itemEl.nextElementSibling;
                const nextTaskId = (nextEl && nextEl.getAttribute('data-id')) ? nextEl.getAttribute('data-id') : null;

                // Send to backend
                fetch('{{ route('tasks.reorder') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        task_id: taskId,
                        status_id: newStatusId,
                        previous_task_id: prevTaskId,
                        next_task_id: nextTaskId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(!data.success) {
                        console.error('Error reordering tasks');
                    } else {
                        // Update UI if the status changed
                        if (evt.from !== evt.to) {
                            const newStatusName = evt.to.closest('.rounded-xl').querySelector('h3 span:last-child').innerText;
                            const statusBadge = itemEl.querySelector('.task-status-badge');
                            if (statusBadge) {
                                statusBadge.innerText = newStatusName;
                            }
                            
                            const oldBadgeCount = evt.from.closest('.rounded-xl').querySelector('.column-task-count');
                            const newBadgeCount = evt.to.closest('.rounded-xl').querySelector('.column-task-count');
                            if (oldBadgeCount && newBadgeCount) {
                                oldBadgeCount.innerText = parseInt(oldBadgeCount.innerText) - 1;
                                newBadgeCount.innerText = parseInt(newBadgeCount.innerText) + 1;
                            }
                            
                            // Update the underlying task payload for the modal
                            try {
                                const taskData = JSON.parse(itemEl.dataset.task);
                                taskData.status_id = parseInt(newStatusId);
                                itemEl.dataset.task = JSON.stringify(taskData);
                            } catch (e) {
                                console.error('Error parsing task dataset', e);
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            },
        });
    });
});
</script>
@endpush
@endsection
