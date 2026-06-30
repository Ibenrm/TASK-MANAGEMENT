<?php

namespace App\Http\Controllers;

use App\Models\StatusNode;
use App\Models\Priority;
use App\Models\User;
use App\Models\Task;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        // Get the 3 fixed status nodes with their tasks
        $statusNodes = StatusNode::with(['tasks.assignees', 'tasks.priority', 'tasks.todos', 'tasks.comments.user'])->orderBy('sort_order')->get();
        $priorities = Priority::orderBy('level')->get();
        $users = User::all();
        
        // Sort tasks for each status node using linked list logic
        foreach ($statusNodes as $status) {
            $tasksById = $status->tasks->keyBy('id');
            $sortedTasks = collect();
            
            // Find head (previous_task_id is null)
            $current = $status->tasks->whereNull('previous_task_id')->first();
            
            // Prevent infinite loops by keeping track of visited nodes
            $visited = [];
            while ($current && !in_array($current->id, $visited)) {
                $sortedTasks->push($current);
                $visited[] = $current->id;
                $current = $current->next_task_id ? $tasksById->get($current->next_task_id) : null;
            }
            
            // Any orphaned tasks (due to broken links) that weren't visited
            $orphans = $status->tasks->whereNotIn('id', $visited);
            foreach ($orphans as $orphan) {
                $sortedTasks->push($orphan);
            }
            
            $status->setRelation('tasks', $sortedTasks);
        }

        return view('tasks', compact('statusNodes', 'priorities', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'status_id' => 'required|exists:status_nodes,id',
            'priority_id' => 'required|exists:priorities,id',
        ]);

        $priorityId = $request->input('priority_id');
        
        // Find the tail task for the given status
        $tailTask = Task::where('status_id', $request->status_id)
            ->whereNull('next_task_id')
            ->first();

        $task = Task::create([
            'title' => $request->title,
            'note' => $request->note,
            'status_id' => $request->status_id,
            'priority_id' => $priorityId,
            'start_date' => $request->start_date,
            'deadline_date' => $request->deadline_date,
            'previous_task_id' => $tailTask ? $tailTask->id : null,
            'next_task_id' => null,
        ]);

        if ($tailTask) {
            $tailTask->update(['next_task_id' => $task->id]);
        }

        if ($request->filled('assignees')) {
            $assigneeIds = explode(',', $request->assignees);
            $task->assignees()->sync($assigneeIds);
        }

        if ($request->filled('todos_json')) {
            $todos = json_decode($request->todos_json, true);
            if (is_array($todos)) {
                foreach ($todos as $index => $todo) {
                    if (trim($todo['todo_text'] ?? '') !== '') {
                        $task->todos()->create([
                            'todo_text' => trim($todo['todo_text']),
                            'is_checked' => $todo['is_checked'] ?? false,
                            'position' => $index,
                        ]);
                    }
                }
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'task_id' => $task->id,
            'action' => 'created',
            'description' => "Membuat tugas baru: {$task->title}",
        ]);

        return redirect()->route('tugas')->with('success', 'Task berhasil dibuat!');
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'status_id' => 'required|exists:status_nodes,id',
            'priority_id' => 'required|exists:priorities,id',
        ]);

        $task->update([
            'title' => $request->title,
            'note' => $request->note,
            'status_id' => $request->status_id,
            'priority_id' => $request->input('priority_id'),
            'start_date' => $request->start_date,
            'deadline_date' => $request->deadline_date,
        ]);

        if ($request->filled('assignees')) {
            $assigneeIds = explode(',', $request->assignees);
            $task->assignees()->sync($assigneeIds);
        } else {
            $task->assignees()->sync([]);
        }

        if ($request->has('todos_json')) {
            $todos = json_decode($request->todos_json, true);
            if (is_array($todos)) {
                $keptIds = [];
                foreach ($todos as $index => $todo) {
                    if (trim($todo['todo_text'] ?? '') !== '') {
                        if (isset($todo['id'])) {
                            $existingTodo = $task->todos()->find($todo['id']);
                            if ($existingTodo) {
                                $existingTodo->update([
                                    'todo_text' => trim($todo['todo_text']),
                                    'is_checked' => filter_var($todo['is_checked'] ?? false, FILTER_VALIDATE_BOOLEAN),
                                    'position' => $index,
                                ]);
                                $keptIds[] = $existingTodo->id;
                            }
                        } else {
                            $newTodo = $task->todos()->create([
                                'todo_text' => trim($todo['todo_text']),
                                'is_checked' => filter_var($todo['is_checked'] ?? false, FILTER_VALIDATE_BOOLEAN),
                                'position' => $index,
                            ]);
                            $keptIds[] = $newTodo->id;
                        }
                    }
                }
                
                $task->todos()->whereNotIn('id', $keptIds)->delete();
            }
        }

        if ($request->filled('new_comment')) {
            $task->comments()->create([
                'user_id' => auth()->id() ?? 1, // fallback if not authenticated in some local env
                'comment_text' => $request->new_comment,
            ]);
        }

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'task_id' => $task->id,
            'action' => 'updated',
            'description' => "Memperbarui tugas: {$task->title}",
        ]);

        return redirect()->route('tugas')->with('success', 'Task berhasil diperbarui!');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'status_id' => 'required|exists:status_nodes,id',
            'previous_task_id' => 'nullable|exists:tasks,id',
            'next_task_id' => 'nullable|exists:tasks,id',
        ]);

        $task = Task::findOrFail($request->task_id);
        $oldPrevId = $task->previous_task_id;
        $oldNextId = $task->next_task_id;

        // Skip if position hasn't changed (same prev, same next, same status)
        if ($oldPrevId == $request->previous_task_id && $oldNextId == $request->next_task_id && $task->status_id == $request->status_id) {
            return response()->json(['success' => true]);
        }

        // 1. Remove task from its old position in the linked list
        if ($oldPrevId) {
            Task::where('id', $oldPrevId)->update(['next_task_id' => $oldNextId]);
        }
        if ($oldNextId) {
            Task::where('id', $oldNextId)->update(['previous_task_id' => $oldPrevId]);
        }

        // 2. Insert task into its new position
        $newPrevId = $request->previous_task_id;
        $newNextId = $request->next_task_id;

        $task->update([
            'status_id' => $request->status_id,
            'previous_task_id' => $newPrevId,
            'next_task_id' => $newNextId,
        ]);

        if ($newPrevId) {
            Task::where('id', $newPrevId)->update(['next_task_id' => $task->id]);
        }
        if ($newNextId) {
            Task::where('id', $newNextId)->update(['previous_task_id' => $task->id]);
        }

        if ($task->status_id != $request->status_id) {
            $oldStatusNode = StatusNode::find($task->status_id);
            $newStatusNode = StatusNode::find($request->status_id);
            if ($oldStatusNode && $newStatusNode) {
                ActivityLog::create([
                    'user_id' => auth()->id() ?? 1,
                    'task_id' => $task->id,
                    'action' => 'moved',
                    'description' => "Memindahkan tugas '{$task->title}' dari {$oldStatusNode->name} ke {$newStatusNode->name}",
                ]);
            }
        }

        return response()->json(['success' => true]);
    }
}
