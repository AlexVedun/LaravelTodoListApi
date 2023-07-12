<?php

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use Carbon\Carbon;
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

    public function updateTask(Task $task, array $taskData): ?Task
    {
        try {
            $task->update($taskData);
            $task->refresh();

            return $task;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            Log::debug($exception->getTraceAsString());
        }

        return null;
    }

    public function deleteTask(Task $task): bool
    {
        try {
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
        return Task::whereUserId($userId)
            ->whereNull('parent_id')
            ->get();
    }

    public function getTask(int $taskId): ?Task
    {
        return Task::find($taskId);
    }

    public function completeTask(Task $task): ?Task
    {
        $taskData = [
            'status' => Task::STATUS_DONE,
            'completed_at' => Carbon::now(),
        ];

        return $this->updateTask($task, $taskData);
    }
}
