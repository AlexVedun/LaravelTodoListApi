<?php

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class TaskRepository implements TaskRepositoryInterface
{
    public function createTask(array $taskData): ?Task
    {
        try {
            return Task::create($taskData);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            Log::debug($exception->getTraceAsString());
        }

        return null;
    }

    public function updateTask(int $taskId, array $taskData): ?Task
    {
        try {
            $task = $this->getTask($taskId);
            $task->update($taskData);
            $task->refresh();

            return $task;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            Log::debug($exception->getTraceAsString());
        }

        return null;
    }

    public function deleteTask(int $taskId): bool
    {
        try {
            $task = $this->getTask($taskId);
            $task->delete();

            return true;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            Log::debug($exception->getTraceAsString());
        }

        return false;
    }

    public function getTasks(int $userId, array $filters): Collection
    {
        return Task::whereUserId($userId)->get();
    }

    public function getTask(int $taskId): ?Task
    {
        return Task::find($taskId);
    }
}
