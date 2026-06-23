<?php

namespace App\Http\Controllers;

use App\Models\StatusNode;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        // Get the 3 fixed status nodes with their tasks
        $statusNodes = StatusNode::with(['tasks.assignees', 'tasks.priority'])->orderBy('sort_order')->get();
        
        return view('tasks', compact('statusNodes'));
    }
}
