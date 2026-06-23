<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusNode extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'next_status_id',
        'sort_order',
    ];

    public function nextStatus()
    {
        return $this->belongsTo(StatusNode::class, 'next_status_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'status_id');
    }
}
