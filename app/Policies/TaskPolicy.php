<?php

namespace App\Policies;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->id == $task->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return ($user->id == $task->user_id) && ($task->status != TaskStatus::DONE) ;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return ($user->id == $task->user_id) && ($task->status != TaskStatus::DONE);
    }

    public function complete(User $user, Task $task): bool
    {
        return !$task->has_undone_subtasks;
    }
}
