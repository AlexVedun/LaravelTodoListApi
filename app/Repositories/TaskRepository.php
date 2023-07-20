<?php

namespace App\Repositories;

use App\DTO\TaskDTO\TaskDTO;
use App\DTO\TaskDTO\TaskFilterDTO;
use App\Enums\TaskStatus;
use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LogicException;

class TaskRepository implements TaskRepositoryInterface
{
    public function createTask(TaskDTO $taskData): Task
    {
        return Task::create($taskData->toArray());
    }

    public function updateTask(Task $task, TaskDTO $taskData): Task
    {
        if (!$task->update($taskData->toArray())) {
            throw new LogicException('Cannot update task model with id=' . $task->id);
        };
        $task->refresh();

        return $task;
    }

    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }

    public function getTasks(int $userId, TaskFilterDTO $filters): Collection
    {
        $tasksQuery = Task::query()
            ->whereUserId($userId)
            ->whereNull('parent_id')
            ->when($filters->status, function (Builder $query) use ($filters) {
                return $query->where('status', $filters->status->value);
            })
            ->when($filters->priority, function (Builder $query) use ($filters) {
                return $query->where('priority', $filters->priority);
            })
            ->when($filters->title, function (Builder $query) use ($filters) {
                return $query->whereFullText('title', $filters->title);
            });

        return $tasksQuery
            ->orderBy($filters->sortBy->value, $filters->sortDirection->value)
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

    public function completeTask(Task $task): Task
    {
        $taskData = TaskDTO::fromModel($task);
        $taskData->status = TaskStatus::DONE;
        $taskData->completedAt = now();

        return $this->updateTask($task, $taskData);
    }
}
