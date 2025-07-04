<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreTagRequest extends FormRequest
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
        $projectId = $this->route('project')?->id ?? $this->tag?->project_id;

        $tagId = $this->tag?->id;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                // Ensure the tag is unique within the project context
                Rule::unique('tags', 'name')
                    ->where('project_id', $projectId)
                    ->ignore($tagId),
            ],
        ];
    }
}
