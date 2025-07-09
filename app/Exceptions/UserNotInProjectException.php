<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\Response;

final class UserNotInProjectException extends ApiException
{
    public function __construct()
    {
        parent::__construct(
            'Cet utilisateur ne fait pas partie du projet.',
            Response::HTTP_FORBIDDEN
        );
    }
}
