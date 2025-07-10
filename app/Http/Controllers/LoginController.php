<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        if (! Auth::attempt($request->safe()->only(['email', 'password']))) {
            throw new InvalidCredentialsException;
        }

        $user = User::query()->where('email', $request->validated('email'))->first();

        $token = $user->createToken('api-token');

        return response()->json(['token' => $token->plainTextToken]);
    }
}
