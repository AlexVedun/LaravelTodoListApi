<?php

namespace App\Interfaces;

use App\DTO\TaskDTO\TaskDTO;
use App\DTO\TaskDTO\TaskFilterDTO;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function createTask(TaskDTO $taskData): Task;
    public function updateTask(Task $task, TaskDTO $taskData): Task;
    public function deleteTask(Task $task): bool;
    public function getTasks(int $userId, TaskFilterDTO $filters): Collection;
    public function getTask(int $taskId): ?Task;
    public function completeTask(Task $task): Task;
}
