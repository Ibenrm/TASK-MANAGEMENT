<?php

namespace App\Http\Controllers;

use App\Models\StatusNode;
use App\Models\Priority;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        // Get the 3 fixed status nodes with their tasks
        $statusNodes = StatusNode::with(['tasks.assignees', 'tasks.priority', 'tasks.todos', 'tasks.comments.user'])->orderBy('sort_order')->get();
        $priorities = Priority::orderBy('level')->get();
        $users = User::all();
        
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
        $task = Task::create([
            'title' => $request->title,
            'note' => $request->note,
            'status_id' => $request->status_id,
            'priority_id' => $priorityId,
            'start_date' => $request->start_date,
            'deadline_date' => $request->deadline_date,
        ]);

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
                            }
                        } else {
                            $task->todos()->create([
                                'todo_text' => trim($todo['todo_text']),
                                'is_checked' => filter_var($todo['is_checked'] ?? false, FILTER_VALIDATE_BOOLEAN),
                                'position' => $index,
                            ]);
                        }
                    }
                }
                
                $keptIds = array_filter(array_column($todos, 'id'));
                $task->todos()->whereNotIn('id', $keptIds)->delete();
            }
        }

        if ($request->filled('new_comment')) {
            $task->comments()->create([
                'user_id' => auth()->id() ?? 1, // fallback if not authenticated in some local env
                'comment_text' => $request->new_comment,
            ]);
        }

        return redirect()->route('tugas')->with('success', 'Task berhasil diperbarui!');
    }
}
