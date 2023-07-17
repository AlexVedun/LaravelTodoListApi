<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'priority',
        'status',
        'title',
        'description',
        'completed_at',
    ];
    protected $casts = [
        'completed_at' => 'datetime',
        'status' => TaskStatus::class,
    ];

    protected $with = [
        'subtasks',
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

    // Attributes

    public function getHasUndoneSubtasksAttribute(): bool
    {
        return $this->subtasks()->where('status', TaskStatus::TODO)->count() > 0;
    }
}
