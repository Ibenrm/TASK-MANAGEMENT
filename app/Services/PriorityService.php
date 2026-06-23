<?php

namespace App\Services;

use App\Models\Priority;

class PriorityService
{
    public function getAll()
    {
        return Priority::all();
    }

    public function getById($id)
    {
        return Priority::findOrFail($id);
    }

    public function create(array $data)
    {
        return Priority::create($data);
    }

    public function update($id, array $data)
    {
        $priority = Priority::findOrFail($id);
        $priority->update($data);
        return $priority;
    }

    public function delete($id)
    {
        $priority = Priority::findOrFail($id);
        return $priority->delete();
    }
}
