<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\Response;

final class UserAlreadyInProjectException extends ApiException
{
    public function __construct()
    {
        parent::__construct(
            'Cet utilisateur est déjà présent dans le projet.',
            Response::HTTP_CONFLICT
        );
    }
}
