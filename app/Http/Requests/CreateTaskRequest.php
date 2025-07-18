<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enum\TaskPoints;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateTaskRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'min:3', 'max:255'],
            'points' => ['required', Rule::enum(TaskPoints::class)],
            'tag_ids' => ['sometimes', 'nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id', 'distinct:strict'],
            'parent_id' => ['sometimes', 'nullable', 'integer', 'exists:tasks,id'],
            'assigned_to_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
        ];
    }
}
