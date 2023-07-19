<?php

namespace App\Repositories;

use App\Enums\TaskSortBy;
use App\Enums\TaskStatus;
use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LogicException;

class TaskRepository implements TaskRepositoryInterface
{
    public function createTask(array $taskData): Task
    {
        $taskData['status'] = TaskStatus::TODO;

        return Task::create($taskData);
    }

    public function updateTask(Task $task, array $taskData): Task
    {
        if (!$task->update($taskData)) {
            throw new LogicException('Cannot update task model with id=' . $task->id);
        };
        $task->refresh();

        return $task;
    }

    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }

    public function getTasks(int $userId, array $filters = []): Collection
    {
        $statusFilter = data_get($filters, 'status');
        $priorityFilter = data_get($filters, 'priority');
        $titleFilter = data_get($filters, 'title');
        $sortBy = TaskSortBy::from(data_get($filters, 'sort_by', TaskSortBy::CREATED_AT->value));
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
                return $query->whereFullText('title', $titleFilter);
            });

        return $tasksQuery
            ->orderBy($sortBy->value, $sortDirection)
            ->get();
    }

    public function getTask(int $taskId): ?Task
    {
        $task = Task::find($taskId);
        if (!$task) {
            throw new ModelNotFoundException('Cannot find task model with id=' . $taskId);
        }

        return $task;
    }

    public function completeTask(Task $task): ?Task
    {
        $taskData = [
            'status' => TaskStatus::DONE,
            'completed_at' => Carbon::now(),
        ];

        return $this->updateTask($task, $taskData);
    }
}
