<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\TaskPoints;
use App\Enum\TaskStatus;
use App\Exceptions\InvalidTaskStatusTransitionException;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Task extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => TaskStatus::TODO->value,
        'number' => null,
    ];

	/**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'points' => TaskPoints::class
        ];
    }

    /**
     * Generate task number
     */
    protected static function booted(): void
    {
        self::creating(fn (Task $task) => $task->number = rand(100000, 999999));

        self::updating(function (Task $task) {
            if ($task->isDirty('status')) {
                $oldStatus = $task->getOriginal('status');

                if (! $oldStatus->canTransitionTo($task->status)) {
                    throw new InvalidTaskStatusTransitionException($oldStatus, $task->status);
                }
            }
        });
    }

    #[Scope]
    protected function onlyParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }
}
