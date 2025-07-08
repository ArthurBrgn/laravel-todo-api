<?php

declare(strict_types=1);

namespace App\Exceptions;

final class ProjectException extends ApiException
{
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}
