<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\StatusNode;
use App\Models\Priority;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['status', 'priority', 'assignees'])->get();
        
        $totalTasks = $tasks->count();

        $tasksByStatus = $tasks->groupBy(function($task) {
            return $task->status ? $task->status->name : 'Unknown';
        })->map->count();

        $tasksByPriority = $tasks->groupBy(function($task) {
            return $task->priority ? $task->priority->name : 'Unknown';
        })->map->count();

        $users = User::withCount('assignedTasks')->get();
        $teamWorkload = $users->mapWithKeys(function ($user) {
            return [$user->full_name => $user->assigned_tasks_count];
        });

        $recentTasks = Task::with(['status', 'priority', 'assignees'])->orderBy('created_at', 'desc')->take(5)->get();

        $today = Carbon::today();
        
        $completedTasks = $tasks->filter(function ($task) {
            return $task->status && in_array(strtolower($task->status->slug), ['done', 'completed', 'selesai']);
        })->count();

        $overdueTasks = $tasks->filter(function ($task) use ($today) {
            $isDone = $task->status && in_array(strtolower($task->status->slug), ['done', 'completed', 'selesai']);
            return !$isDone && $task->deadline_date && Carbon::parse($task->deadline_date)->lt($today);
        })->count();

        return view('dashboard', [
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'overdueTasks' => $overdueTasks,
            'tasksByStatus' => $tasksByStatus,
            'tasksByPriority' => $tasksByPriority,
            'teamWorkload' => $teamWorkload,
            'recentTasks' => $recentTasks,
        ]);
    }
}
