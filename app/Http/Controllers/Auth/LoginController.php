<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->safe()->only(['email', 'password']))) {
            throw new InvalidCredentialsException;
        }

        $user = User::query()->where('email', $request->validated('email'))->first();

        $token = $user->createToken('api-token');

        return response()->json(['token' => $token->plainTextToken]);
    }
}
