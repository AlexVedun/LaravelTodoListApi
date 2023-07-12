<?php

namespace App\Http\Requests\TaskRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTaskRequest extends FormRequest
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
            'priority' => ['required', 'int', Rule::in([1, 2, 3, 4, 5])],
            'title' => ['required', 'string', 'max:250'],
            'description' => ['required', 'string'],
            'parent_id' => ['nullable', 'int', 'exists:tasks,id'],
        ];
    }
}
