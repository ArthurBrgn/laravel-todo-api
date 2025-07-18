<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class LogoutController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
