@extends('layouts.app')

@section('title', 'Tugas - Task Manager')
@section('header', 'Tugas')

@section('content')
<div class="h-full flex flex-col min-h-0">
    <!-- Header/Actions -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kanban Board</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola tugas Anda berdasarkan status penyelesaian.</p>
        </div>
        <button class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
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
        <div class="bg-gray-50 rounded-xl flex flex-col h-full border border-gray-200 max-h-[80vh] overflow-hidden shadow-sm">
            <!-- Column Header -->
            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-100/50">
                <h3 class="font-semibold text-gray-700 flex items-center gap-2">
                    <!-- Dynamic Dot Color based on Status -->
                    @php
                        $dotColor = 'bg-gray-400';
                        if($status->slug == 'to_do') $dotColor = 'bg-yellow-400';
                        if($status->slug == 'in_progress') $dotColor = 'bg-blue-500';
                        if($status->slug == 'done') $dotColor = 'bg-green-500';
                    @endphp
                    <span class="w-2.5 h-2.5 rounded-full {{ $dotColor }}"></span>
                    {{ $status->name }}
                </h3>
                <span class="bg-white text-gray-600 text-xs font-bold px-2.5 py-1 rounded-full shadow-sm border border-gray-200">
                    {{ $status->tasks->count() }}
                </span>
            </div>

            <!-- Task List -->
            <div class="p-3 flex-1 overflow-y-auto space-y-3">
                @forelse($status->tasks as $task)
                <!-- Task Card -->
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 cursor-pointer group hover:border-blue-300">
                    <!-- Badges -->
                    <div class="flex justify-between items-start mb-3">
                        @php
                            $pName = $task->priority ? strtolower($task->priority->name) : '';
                            $pClass = 'bg-gray-100 text-gray-800';
                            if(str_contains($pName, 'high') || str_contains($pName, 'tinggi')) $pClass = 'bg-red-100 text-red-800';
                            elseif(str_contains($pName, 'med') || str_contains($pName, 'sedang')) $pClass = 'bg-yellow-100 text-yellow-800';
                            elseif(str_contains($pName, 'low') || str_contains($pName, 'rendah')) $pClass = 'bg-green-100 text-green-800';
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $pClass }}">
                            {{ $task->priority ? $task->priority->name : 'No Priority' }}
                        </span>
                        
                        <button class="text-gray-400 hover:text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                        </button>
                    </div>

                    <!-- Title -->
                    <h4 class="font-medium text-gray-800 mb-1.5 leading-snug">{{ $task->title }}</h4>
                    
                    @if($task->note)
                    <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ $task->note }}</p>
                    @endif

                    <!-- Footer: Date & Assignees -->
                    <div class="mt-4 flex items-center justify-between border-t border-gray-50 pt-3">
                        <div class="flex items-center text-xs text-gray-500 gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="{{ $task->deadline_date && \Carbon\Carbon::parse($task->deadline_date)->isPast() && $status->slug != 'done' ? 'text-red-500 font-semibold' : '' }}">
                                {{ $task->deadline_date ? \Carbon\Carbon::parse($task->deadline_date)->format('d M') : '-' }}
                            </span>
                        </div>

                        <!-- Assignees -->
                        <div class="flex items-center -space-x-1.5">
                            @forelse($task->assignees as $assignee)
                                <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center text-[10px] font-bold border-2 border-white shadow-sm ring-1 ring-black/5" title="{{ $assignee->full_name }}">
                                    {{ strtoupper(substr($assignee->full_name, 0, 1)) }}
                                </div>
                            @empty
                                <div class="w-6 h-6 rounded-full bg-gray-100 border-2 border-white shadow-sm ring-1 ring-black/5" title="Unassigned"></div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center p-6 text-sm text-gray-400 border-2 border-dashed border-gray-200 rounded-lg bg-gray-50/50">
                    Tidak ada tugas
                </div>
                @endforelse
            </div>
        </div>
        @empty
        <div class="col-span-3 bg-yellow-50 text-yellow-800 p-4 rounded-lg flex items-center gap-3">
            <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            Status belum di-seed. Silakan jalankan seeder di terminal.
        </div>
        @endforelse
    </div>
</div>
@endsection
