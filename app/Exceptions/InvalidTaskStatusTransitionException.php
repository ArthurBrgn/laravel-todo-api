<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enum\TaskStatus;

final class InvalidTaskStatusTransitionException extends ApiException
{
    public function __construct(TaskStatus $oldStatus, TaskStatus $newStatus)
    {
        parent::__construct(
            "Transtion du statut {$oldStatus->value} à {$newStatus->value} impossible",
            409
        );
    }
}
