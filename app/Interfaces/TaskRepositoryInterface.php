<?php

namespace App\Interfaces;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function createTask(array $taskData): Task;
    public function updateTask(Task $task, array $taskData): Task;
    public function deleteTask(Task $task): bool;
    public function getTasks(int $userId, array $filters = []): Collection;
    public function getTask(int $taskId): ?Task;
    public function completeTask(Task $task): ?Task;
}
