<?php

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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

    public function getTasks(int $userId, array $filters = []): Collection
    {
        $statusFilter = data_get($filters, 'status');
        $priorityFilter = data_get($filters, 'priority');
        $titleFilter = data_get($filters, 'title');
        $sortBy = data_get($filters, 'sort_by', Task::SORT_CREATED_AT);
        $sortDirection = data_get($filters, 'sort_direction', 'desc');

        $tasksQuery = Task::whereUserId($userId)
            ->whereNull('parent_id')
            ->when($statusFilter, function (Builder $query) use ($statusFilter) {
                return $query->where('status', $statusFilter);
            })
            ->when($priorityFilter, function (Builder $query) use ($priorityFilter) {
                return $query->where('priority', $priorityFilter);
            })
            ->when($titleFilter, function (Builder $query) use ($titleFilter) {
                return $query->where('title', 'like', "%${titleFilter}%");
            });

        $sortField = match ($sortBy) {
            Task::SORT_PRIORITY => 'priority',
            Task::SORT_COMPLETED_AT => 'completed_at',
            default => 'created_at',
        };

        return $tasksQuery
            ->orderBy($sortField, $sortDirection)
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
