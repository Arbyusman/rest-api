<?php

namespace Tests\Feature;

use App\Enums\Roles;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $url = '/api/v1/users';

    protected function setUp(): void
    {
        parent::setUp();

        Role::Insert([
            ['name' => 'Manager'],
            ['name' => 'Staff'],
        ]);
    }

    /** @test */
    public function can_create_user()
    {

        $manager = User::create([
            'name' => 'manager',
            'email' => 'manager@gmail.com',
            'password' => '1245789',
            'role_id' => Roles::Manager->value,
            'manager_id' => 1,
        ]);

        $response = $this->postJson($this->url, [
            'name' => 'staff',
            'email' => 'staff@gmail.com',
            'password' => 'rahasia1',
            'role_id' => Roles::Manager->value,
            'manager_id' => $manager->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'metadata' => [
                    'status' => 201,
                    'message' => 'User created successfully',
                ],
            ])
            ->assertJsonStructure([
                'metadata' => ['status', 'message'],
            ]);
    }

    /** @test */
    public function create_user_with_email_already_been_taken()
    {

        $input = [
            'name' => 'manager',
            'email' => 'manager@gmail.com',
            'password' => '1245678',
            'role_id' => Roles::Manager->value,
            'manager_id' => null,
        ];

        User::create($input);

        $response = $this->postJson($this->url, $input);

        $response->assertStatus(422)
            ->assertJson([
                'metadata' => [
                    'status' => 422,
                    'message' => 'Validation Error',
                    'error' => [
                        'email' => ['The email has already been taken.'],
                    ],
                ],
            ]);
    }

    /** @test */
    public function create_user_with_password_not_valid()
    {
        $response = $this->postJson($this->url, [
            'name' => 'manager',
            'email' => 'manager@gmail.com',
            'password' => '1245',
            'role_id' => Roles::Manager->value,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'metadata' => [
                    'status' => 422,
                    'message' => 'Validation Error',
                    'error' => [
                        'password' => ['The password field must be at least 6 characters.'],
                    ],
                ],
            ]);
    }

    /** @test */
    public function create_user_id_manager_id_is_required()
    {

        $response = $this->postJson($this->url, [
            'name' => 'staff',
            'email' => 'staff@gmail.com',
            'password' => '1245789',
            'role_id' => Roles::Staff->value,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'metadata' => [
                    'status' => 422,
                    'message' => 'Validation Error',
                    'error' => [
                        'manager_id' => ['The manager_id field is required for staff'],
                    ],
                ],
            ]);
    }

    /** @test */
    public function create_user_manager_id_is_not_a_manager_role()
    {

        $staf = User::create([
            'name' => 'staff',
            'email' => 'staff@gmail.com',
            'password' => '1245789',
            'role_id' => Roles::Staff->value,
            'manager_id' => 1,
        ]);

        $response = $this->postJson($this->url, [
            'name' => 'staff2',
            'email' => 'staff2@gmail.com',
            'password' => '1245789',
            'role_id' => Roles::Staff->value,
            'manager_id' => $staf->id,
        ]);

        $response->assertStatus(status: 500)
            ->assertJson([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                    'error' => 'The selected manager_id must be a user with Manager role.',
                ],
            ]);
    }
}
