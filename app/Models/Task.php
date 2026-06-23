<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'note',
        'status_id',
        'priority_id',
        'start_date',
        'deadline_date',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'deadline_date' => 'date',
        ];
    }

    public function status()
    {
        return $this->belongsTo(StatusNode::class, 'status_id');
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class, 'priority_id');
    }

    public function todos()
    {
        return $this->hasMany(TaskTodo::class, 'task_id');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'task_id');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_assignees', 'task_id', 'user_id')
                    ->withPivot('assigned_at');
    }
}
