<?php

namespace App\Services;

use App\Models\TaskTodo;

class TaskTodoService
{
    public function getAll()
    {
        return TaskTodo::with('task')->orderBy('position')->get();
    }

    public function getById($id)
    {
        return TaskTodo::with('task')->findOrFail($id);
    }

    public function getByTaskId($taskId)
    {
        return TaskTodo::where('task_id', $taskId)->orderBy('position')->get();
    }

    public function create(array $data)
    {
        return TaskTodo::create($data);
    }

    public function update($id, array $data)
    {
        $taskTodo = TaskTodo::findOrFail($id);
        $taskTodo->update($data);
        return $taskTodo;
    }

    public function delete($id)
    {
        $taskTodo = TaskTodo::findOrFail($id);
        return $taskTodo->delete();
    }
}
