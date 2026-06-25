@extends('layouts.app')

@section('title', 'Activity History - Task Manager')
@section('header', 'Activity History')

@section('content')
<div class="max-w-4xl mx-auto py-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white tracking-tight">Riwayat Aktivitas</h2>
        <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-indigo-50 dark:bg-white text-indigo-600 dark:text-indigo-900 px-3 py-1.5 rounded-full shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
            </svg>
            LIFO Stack
        </span>
    </div>
    @if(isset($logs) && $logs->count() > 0)
        {{-- Timeline --}}
        <div class="mt-8" style="position: relative; padding-left: 2rem;">

            {{-- The Vertical Line (Guaranteed to show via inline style) --}}
            <div style="position: absolute; left: 10px; top: 1.5rem; bottom: 1.5rem; width: 2px; background-color: #404040; border-radius: 9999px;"></div>

            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                @foreach($logs as $index => $log)

                    @php
                        $action = strtolower($log->action ?? 'default');
                        $dotColors = [
                            'created' => '#10b981', // Emerald
                            'updated' => '#3b82f6', // Blue
                            'moved'   => '#f59e0b', // Amber
                            'deleted' => '#ef4444', // Red
                        ];
                        $badgeBg = [
                            'created' => ['bg' => '#a7f3d0', 'text' => '#064e3b'],
                            'updated' => ['bg' => '#bfdbfe', 'text' => '#1e3a8a'],
                            'moved'   => ['bg' => '#fde68a', 'text' => '#92400e'],
                            'deleted' => ['bg' => '#fecaca', 'text' => '#7f1d1d'],
                        ];
                        
                        $dotColor = $dotColors[$action] ?? '#8b5cf6';
                        $bBg = $badgeBg[$action]['bg'] ?? '#ddd6fe';
                        $bText = $badgeBg[$action]['text'] ?? '#4c1d95';
                        $userName = $log->user ? $log->user->name : 'Sistem';
                    @endphp

                    <div style="position: relative; width: 100%;">
                        
                        {{-- Dot (Absolute positioned over the line) --}}
                        <div style="position: absolute; left: -26px; top: 24px; width: 10px; height: 10px; border-radius: 50%; background-color: {{ $dotColor }}; z-index: 10; box-shadow: 0 0 0 4px #0f172a;"></div>

                        {{-- Card --}}
                        <div style="background-color: #252525; border: 1px solid #333333; border-radius: 0.75rem; padding: 1.25rem; transition: background-color 0.2s;">
                            
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.75rem;">
                                {{-- Left: name + badge --}}
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <span style="font-size: 17px; font-weight: 700; color: #ffffff;">{{ $userName }}</span>
                                    <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.25rem 0.75rem; border-radius: 9999px; background-color: {{ $bBg }}; color: {{ $bText }};">
                                        {{ $log->action }}
                                    </span>
                                </div>

                                {{-- Right: Timestamp --}}
                                <span style="font-size: 13px; font-weight: 500; color: #888888; white-space: nowrap;">
                                    {{ $log->created_at->diffForHumans() }}
                                </span>
                            </div>

                            {{-- Description --}}
                            <p style="font-size: 15px; color: #bbbbbb; font-weight: 500; line-height: 1.6; margin: 0;">
                                {{ $log->description }}
                            </p>

                        </div>
                    </div>

                @endforeach
            </div>
        </div>
            
            {{-- Pagination Links --}}
            <div class="mt-8 pt-4 border-t border-gray-100 dark:border-slate-700">
                {{ $logs->links() }}
            </div>
        </div>

    @else

        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-14 border border-dashed border-gray-200 dark:border-slate-700 rounded-xl text-center">
            <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Stack riwayat aktivitas kosong</p>
            <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Aktivitas pengguna akan muncul di sini</p>
        </div>

    @endif
</div>
@endsection