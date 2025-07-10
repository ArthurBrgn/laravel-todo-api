<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\Response;

final class InvalidCredentialsException extends ApiException
{
    public function __construct()
    {
        parent::__construct(
            trans('auth.failed'),
            Response::HTTP_UNAUTHORIZED
        );
    }
}
