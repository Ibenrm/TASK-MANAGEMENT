<?php

namespace App\Services;

use App\Models\TaskAssignee;

class TaskAssigneeService
{
    public function getAll()
    {
        return TaskAssignee::with(['task', 'user'])->get();
    }

    public function getById($id)
    {
        return TaskAssignee::with(['task', 'user'])->findOrFail($id);
    }

    public function getByTaskId($taskId)
    {
        return TaskAssignee::with('user')->where('task_id', $taskId)->get();
    }

    public function create(array $data)
    {
        return TaskAssignee::create($data);
    }

    public function update($id, array $data)
    {
        $taskAssignee = TaskAssignee::findOrFail($id);
        $taskAssignee->update($data);
        return $taskAssignee;
    }

    public function delete($id)
    {
        $taskAssignee = TaskAssignee::findOrFail($id);
        return $taskAssignee->delete();
    }
}
