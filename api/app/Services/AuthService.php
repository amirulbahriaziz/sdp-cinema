<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;

/**
 * Authentication business logic: account creation, credential verification and
 * Sanctum token issuance/revocation. Stateless — every request authenticates
 * from the DB-backed bearer token, never a session.
 */
class AuthService
{
    public const TOKEN_NAME = 'api';

    /**
     * Register a new account and mint its first bearer token.
     *
     * @param  array{name:string,email:string,password:string}  $data
     * @return array{user:User,token:NewAccessToken}
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return [
            'user' => $user,
            'token' => $user->createToken(self::TOKEN_NAME),
        ];
    }

    /**
     * Verify credentials and mint a bearer token. A bad email/password is an
     * authentication failure surfaced as a 422 with the consistent error shape.
     *
     * @param  array{email:string,password:string}  $data
     * @return array{user:User,token:NewAccessToken}
     *
     * @throws ValidationException
     */
    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return [
            'user' => $user,
            'token' => $user->createToken(self::TOKEN_NAME),
        ];
    }

    /**
     * Revoke the token that authenticated the current request (logout).
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
