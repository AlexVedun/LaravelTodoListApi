<?php

namespace App\DTO\TaskDTO;

use App\Enums\SortDirection;
use App\Enums\TaskSortBy;
use App\Enums\TaskStatus;
use App\Http\Requests\TaskRequests\TasksFilterRequest;

class TaskFilterDTO
{
    public function __construct(
        public ?TaskStatus $status,
        public ?int $priority,
        public ?string $title,
        public ?TaskSortBy $sortBy,
        public ?SortDirection $sortDirection,
    ){}

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'priority' => $this->priority,
            'title' => $this->title,
            'sort_by' => $this->sortBy,
            'sort_direction' => $this->sortDirection,
        ];
    }

    public static function fromRequest(TasksFilterRequest $request): TaskFilterDTO
    {
        return new TaskFilterDTO(
            $request->has('status') ? TaskStatus::from($request->get('status')) : null,
            $request->get('priority'),
            $request->get('title'),
            TaskSortBy::from($request->get('sort_by', TaskSortBy::CREATED_AT->value)),
            SortDirection::from($request->get('sort_direction', SortDirection::DESC->value))
        );
    }
}
