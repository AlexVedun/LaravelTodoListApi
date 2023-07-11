<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    public const STATUS_TODO = 'todo';
    public const STATUS_DONE = 'done';

    protected $fillable = [
        'user_id',
        'parent_id',
        'priority',
        'status',
        'title',
        'description',
    ];
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // Relations

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id', 'id');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id', 'id');
    }
}
