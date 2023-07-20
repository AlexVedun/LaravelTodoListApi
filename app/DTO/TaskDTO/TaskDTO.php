<?php

namespace App\DTO\TaskDTO;

use App\Enums\TaskStatus;
use App\Http\Requests\TaskRequests\TaskRequest;
use App\Models\Task;
use Carbon\Carbon;

class TaskDTO
{
    public TaskStatus $status;
    public ?Carbon $completedAt;

    public function __construct(
        public string $title,
        public string $description,
        public int $priority,
        public int $userId,
        public ?int $parentId,
    )
    {
        $this->status = TaskStatus::TODO;
        $this->completedAt = null;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'user_id' => $this->userId,
            'parent_id' => $this->parentId,
            'status' => $this->status,
            'completed_at' => $this->completedAt,
        ];
    }

    public static function fromRequest(TaskRequest $request, int $userId): TaskDTO
    {
        return new TaskDTO(
            $request->get('title'),
            $request->get('description'),
            $request->get('priority'),
            $userId,
            $request->get('parent_id')
        );
    }

    public static function fromModel(Task $task): TaskDTO
    {
        return new TaskDTO(
            $task->title,
            $task->description,
            $task->priority,
            $task->user_id,
            $task->parent_id
        );
    }
}
