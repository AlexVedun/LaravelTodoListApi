<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    public const STATUS_TODO = 'todo';
    public const STATUS_DONE = 'done';

    public const SORT_CREATED_AT = 'created_at';
    public const SORT_COMPLETED_AT = 'completed_at';
    public const SORT_PRIORITY = 'priority';

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
        return $this->subtasks()->where('status', self::STATUS_TODO)->count() > 0;
    }
}
