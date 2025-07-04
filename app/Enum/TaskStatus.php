<?php

declare(strict_types=1);

namespace App\Enum;

enum TaskStatus: string
{
    case TODO = 'TODO';
    case DOING = 'DOING';
    case REVIEW = 'REVIEW';
    case BLOCKED = 'BLOCKED';
    case DONE = 'DONE';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Retourne les statuts vers lesquels on peut transitionner.
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::TODO => [self::DOING, self::BLOCKED],
            self::DOING => [self::REVIEW, self::BLOCKED],
            self::REVIEW => [self::DOING, self::DONE, self::BLOCKED],
            self::BLOCKED => [self::TODO, self::DOING],
            self::DONE => [],
        };
    }

    /**
     * Vérifie si la transition vers un autre statut est permise.
     */
    public function canTransitionTo(self $targetStatus): bool
    {
        return in_array($targetStatus, $this->allowedTransitions(), true);
    }
}
