<?php

namespace App\Http\Requests\TaskRequests;

use App\Enums\SortDirection;
use App\Enums\TaskSortBy;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class TasksFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', new Enum(TaskStatus::class)],
            'priority' => ['nullable', 'int', Rule::in([1, 2, 3, 4, 5])],
            'title' => ['nullable', 'string'],
            'sort_by' => ['nullable', 'string', new Enum(TaskSortBy::class)],
            'sort_direction' => ['nullable', 'string', new Enum(SortDirection::class)],
        ];
    }
}
