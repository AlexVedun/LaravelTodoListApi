<?php

namespace App\Http\Requests\TaskRequests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetTasksRequest extends FormRequest
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
        $statusVariants = [Task::STATUS_DONE, Task::STATUS_TODO];
        $sortByVariants = [Task::SORT_CREATED_AT, Task::SORT_COMPLETED_AT, Task::SORT_PRIORITY];

        return [
            'status' => ['nullable', 'string', Rule::in($statusVariants)],
            'priority' => ['nullable', 'int', Rule::in([1, 2, 3, 4, 5])],
            'title' => ['nullable', 'string'],
            'sort_by' => ['nullable', 'string', Rule::in($sortByVariants)],
            'sort_direction' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ];
    }
}
