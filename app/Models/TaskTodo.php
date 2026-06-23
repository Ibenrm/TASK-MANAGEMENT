<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskTodo extends Model
{
    protected $fillable = [
        'task_id',
        'todo_text',
        'is_checked',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'is_checked' => 'boolean',
        ];
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
