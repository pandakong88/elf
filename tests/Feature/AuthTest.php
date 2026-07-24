<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run all seeders in the correct order
        $this->seed();
    }

    public function test_login_with_correct_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'admin@elvith.id',
            'password' => 'rahasia123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'token_type',
                'expires_in',
                'user' => [
                    'id',
                    'email',
                    'is_active',
                    'person',
                    'roles',
                ],
            ]);
    }

    public function test_login_with_incorrect_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'admin@elvith.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Email atau password salah.',
            ]);
    }

    public function test_me_endpoint_requires_auth(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }

    public function test_me_endpoint_returns_user_info_with_valid_token(): void
    {
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email'    => 'admin@elvith.id',
            'password' => 'rahasia123',
        ]);

        $token = $loginResponse->json('token');

        $response = $this->getJson('/api/v1/auth/me', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'email',
                    'is_active',
                    'person',
                    'roles',
                    'permissions',
                    'organization_ids',
                ]
            ])
            ->assertJsonPath('data.email', 'admin@elvith.id');
    }

    public function test_core_endpoint_requires_jwt_auth(): void
    {
        $response = $this->getJson('/api/v1/core/persons');

        $response->assertStatus(401);
    }

    public function test_core_endpoint_allows_access_with_valid_token(): void
    {
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email'    => 'admin@elvith.id',
            'password' => 'rahasia123',
        ]);

        $token = $loginResponse->json('token');

        $response = $this->getJson('/api/v1/core/persons', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
    }
}
