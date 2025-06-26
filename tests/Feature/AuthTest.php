<?php

namespace Tests\Feature;

use App\Enums\Roles;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $password = 'pass1234';

    protected $urlLogin = '/api/v1/auth/login';

    protected $urlLogOut = '/api/v1/auth/logout';

    protected function setUp(): void
    {
        parent::setUp();

        Role::Insert([
            ['name' => 'Manager'],
            ['name' => 'Staff'],
        ]);

        $this->user = User::create([
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => $this->password,
            'role_id' => Roles::Manager->value,
            'manager_id' => null,
        ]);
    }

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $response = $this->postJson($this->urlLogin, [
            'email' => $this->user->email,
            'password' => $this->password,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'metadata' => [
                    'status' => 200,
                    'message' => 'User login successfully',
                ],
            ])
            ->assertJsonStructure([
                'metadata' => ['status', 'message'],
                'data' => ['token'],
            ]);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $response = $this->postJson($this->urlLogin, [
            'email' => $this->user->email,
            'password' => '12234',
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                    'error' => 'The provided credentials are incorrect.',
                ],
            ]);
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson($this->urlLogOut);

        $response->assertStatus(200)
            ->assertJson([
                'metadata' => [
                    'status' => 200,
                    'message' => 'Logout successful',
                ],
            ]);
    }
}
