<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Stateless Sanctum token auth: register/login mint a bearer token, logout
 * revokes the one used, and mutating routes reject missing/stale tokens.
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_a_user_and_returns_a_token(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Aisyah',
            'email' => 'aisyah@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.user.email', 'aisyah@example.com')
            ->assertJsonStructure(['data' => ['user' => ['id', 'name', 'email'], 'token']]);

        $this->assertDatabaseHas('users', ['email' => 'aisyah@example.com']);
    }

    public function test_register_rejects_a_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->postJson('/api/auth/register', [
            'name' => 'Dup',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(422)->assertJsonValidationErrorFor('email');
    }

    public function test_login_with_valid_credentials_returns_a_token(): void
    {
        User::factory()->create([
            'email' => 'aisyah@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'aisyah@example.com',
            'password' => 'password123',
        ])->assertOk()->assertJsonStructure(['data' => ['token']]);
    }

    public function test_login_with_wrong_password_is_rejected(): void
    {
        User::factory()->create([
            'email' => 'aisyah@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'aisyah@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(422)->assertJsonValidationErrorFor('email');
    }

    public function test_logout_revokes_the_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;
        $auth = ['Authorization' => "Bearer {$token}"];

        $this->getJson('/api/auth/me', $auth)->assertOk();
        $this->postJson('/api/auth/logout', [], $auth)->assertOk();

        // The token row is gone, so any later request bearing it is rejected.
        // (Asserted at the DB level: Sanctum's guard memoizes the resolved user
        // within a single test process, so a re-hit here would be a false pass.)
        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_protected_route_requires_a_token(): void
    {
        $this->getJson('/api/auth/me')->assertUnauthorized();
    }
}
