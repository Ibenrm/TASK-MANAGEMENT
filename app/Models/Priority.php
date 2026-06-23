<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'level',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'priority_id');
    }
}
