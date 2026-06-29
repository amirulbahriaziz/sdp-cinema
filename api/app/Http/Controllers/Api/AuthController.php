<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Stateless Sanctum token auth. Thin controller — credential logic lives in
 * AuthService; validation + authorization live in the Form Requests.
 */
class AuthController extends Controller
{
    public function __construct(private readonly AuthService $auth) {}

    /**
     * Register.
     *
     * Create an account and return a Sanctum bearer token. Send the token as
     * `Authorization: Bearer {token}` on every mutating request.
     *
     * @group Auth
     *
     * @unauthenticated
     *
     * @bodyParam name string required The user's name. Example: Aisyah
     * @bodyParam email string required A unique email. Example: aisyah@example.com
     * @bodyParam password string required Min 8 chars. Example: password123
     * @bodyParam password_confirmation string required Must match password. Example: password123
     *
     * @response 201 {"data":{"user":{"id":1,"name":"Aisyah","email":"aisyah@example.com"},"token":"1|abcDEF..."}}
     * @response 422 {"message":"The email has already been taken.","errors":{"email":["The email has already been taken."]}}
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->auth->register($request->validated());

        return $this->respondWithToken($result, 201);
    }

    /**
     * Log in.
     *
     * Verify credentials and return a fresh Sanctum bearer token.
     *
     * @group Auth
     *
     * @unauthenticated
     *
     * @bodyParam email string required Example: aisyah@example.com
     * @bodyParam password string required Example: password123
     *
     * @response 200 {"data":{"user":{"id":1,"name":"Aisyah","email":"aisyah@example.com"},"token":"2|abcDEF..."}}
     * @response 422 {"message":"The provided credentials are incorrect.","errors":{"email":["The provided credentials are incorrect."]}}
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->auth->login($request->validated());

        return $this->respondWithToken($result, 200);
    }

    /**
     * Log out.
     *
     * Revoke the bearer token used on this request. Other tokens stay valid.
     *
     * @group Auth
     *
     * @authenticated
     *
     * @response 200 {"data":{"message":"Logged out."}}
     */
    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout($request->user());

        return response()->json(['data' => ['message' => 'Logged out.']]);
    }

    /**
     * The authenticated user.
     *
     * Return the account bound to the bearer token. Useful to rehydrate session
     * state on app launch.
     *
     * @group Auth
     *
     * @authenticated
     *
     * @response 200 {"data":{"id":1,"name":"Aisyah","email":"aisyah@example.com"}}
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json(['data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]]);
    }

    /**
     * @param  array{user:\App\Models\User,token:\Laravel\Sanctum\NewAccessToken}  $result
     */
    private function respondWithToken(array $result, int $status): JsonResponse
    {
        return response()->json(['data' => [
            'user' => [
                'id' => $result['user']->id,
                'name' => $result['user']->name,
                'email' => $result['user']->email,
            ],
            'token' => $result['token']->plainTextToken,
        ]], $status);
    }
}
