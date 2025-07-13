<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class GetProjectTasksRequest extends FormRequest
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
            'user_ids' => ['sometimes', 'nullable', 'array', 'distinct:strict', 'exists:users,id'],
            'tag_ids' => ['sometimes', 'nullable', 'array', 'distinct:strict', 'exists:tags,id'],
        ];
    }
}
