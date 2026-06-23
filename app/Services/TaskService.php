<?php

namespace App\Services;

use App\Models\Task;

class TaskService
{
    public function getAll()
    {
        return Task::with(['status', 'priority', 'todos', 'assignees'])->get();
    }

    public function getById($id)
    {
        return Task::with(['status', 'priority', 'todos', 'assignees'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Task::create($data);
    }

    public function update($id, array $data)
    {
        $task = Task::findOrFail($id);
        $task->update($data);
        return $task;
    }

    public function delete($id)
    {
        $task = Task::findOrFail($id);
        return $task->delete();
    }
}
