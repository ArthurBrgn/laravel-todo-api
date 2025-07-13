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
            'description' => ['nullable', 'string', 'min:3', 'max:255'],
            'points' => ['required', Rule::enum(TaskPoints::class)],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
            'parent_id' => ['nullable', 'exists:tasks,id'],
            'assigned_to_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
