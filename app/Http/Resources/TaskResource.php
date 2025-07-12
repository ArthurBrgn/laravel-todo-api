<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'assigned_to' => new UserResource($this->whenLoaded('assignedTo')),
            'sub_tasks' => TaskResource::collection($this->whenLoaded('subTasks')),
            'parent' => new TaskResource($this->whenLoaded('parent')),
            'sub_tasks_count' => $this->whenCounted('subTasks'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
