<?php

namespace App\Services;

use App\Models\StatusNode;

class StatusNodeService
{
    public function getAll()
    {
        return StatusNode::orderBy('sort_order')->get();
    }

    public function getById($id)
    {
        return StatusNode::findOrFail($id);
    }

    public function create(array $data)
    {
        return StatusNode::create($data);
    }

    public function update($id, array $data)
    {
        $statusNode = StatusNode::findOrFail($id);
        $statusNode->update($data);
        return $statusNode;
    }

    public function delete($id)
    {
        $statusNode = StatusNode::findOrFail($id);
        return $statusNode->delete();
    }
}
