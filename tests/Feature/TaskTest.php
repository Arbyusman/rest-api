<?php

namespace Tests\Feature;

use App\Enums\Roles;
use App\Enums\Statuses;
use App\Models\Role;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected $url;

    protected $user;

    protected $assignee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->url = '/api/v1/tasks';

        Role::Insert([
            ['name' => 'Manager'],
            ['name' => 'Staff'],
        ]);

        Status::insert([
            ['name' => 'To Do'],
            ['name' => 'Doing'],
            ['name' => 'Done'],
            ['name' => 'Canceled'],
        ]);

        $this->user = User::create([
            'name' => 'manager',
            'email' => 'manager@gmail.com',
            'password' => 12345678,
            'role_id' => Roles::Manager->value,
            'manager_id' => null,
        ]);

        $this->assignee = User::create([
            'name' => 'staff',
            'email' => 'staff@gmail.com',
            'password' => 12345678,
            'role_id' => Roles::Staff->value,
            'manager_id' => 1,
        ]);

        Sanctum::actingAs($this->user);
        Sanctum::actingAs($this->assignee);
    }

    /** @test */
    public function user_can_create_task()
    {
        $input = [
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'status_id' => Statuses::ToDo->value,
        ];

        $response = $this->postJson($this->url, $input);

        $response->assertStatus(201)
            ->assertJson([
                'metadata' => [
                    'status' => 201,
                    'message' => 'Task created successfully',
                ],
            ]);
    }

    /** @test */
    public function user_can_create_task_with_null_assigne()
    {
        $input = [
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => null,
            'status_id' => Statuses::ToDo->value,
        ];

        $response = $this->postJson($this->url, $input);

        $response->assertStatus(201)
            ->assertJson([
                'metadata' => [
                    'status' => 201,
                    'message' => 'Task created successfully',
                ],
            ]);
    }

    /** @test */
    public function staf_cannot_create_task_for_other_users()
    {
        $input = [
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->user->id,
            'creator_id' => $this->assignee->id,
            'status_id' => Statuses::ToDo->value,
        ];

        $response = $this->postJson($this->url, $input);

        $response->assertStatus(500)
            ->assertJson([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                    'error' => 'You cannot create tasks for others',
                ],
            ]);
    }

    /** @test */
    public function user_can_show_detail_task()
    {
        $task = Task::create([
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::ToDo->value,
        ]);

        $response = $this->getJson("$this->url/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'metadata' => [
                    'status' => 200,
                    'message' => 'Task retrieved successfully',
                ],
            ])
            ->assertJsonStructure([
                'metadata' => ['status', 'message'],
                'data' => [[
                    'id',
                    'title',
                    'description',
                    'status_id',
                    'status' => [
                        'name',
                    ],
                    'creator_id',
                    'creator' => [
                        'name',
                    ],
                    'assignee_id',
                    'assignee' => [
                        'name',
                    ],
                    'report',
                ]],
            ]);
    }

    /** @test */
    public function cannot_update_task_if_creator_id_is_not_creator()
    {

        $task = Task::create([
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::ToDo->value,
        ]);
        $input = [
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->assignee->id,
            'status_id' => Statuses::ToDo->value,
        ];

        $response = $this->putJson("$this->url/{$task->id}", $input);

        $response->assertStatus(500)
            ->assertJson([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                    'error' => 'Only can be update by the creator',
                ],
            ]);
    }

    /** @test */
    public function cannot_update_when_its_doing_or_done()
    {

        $user = User::create([
            'name' => 'manager4',
            'email' => 'manager4@gmail.com',
            'password' => 12345678,
            'role_id' => Roles::Manager->value,
            'manager_id' => null,
        ]);

        Sanctum::actingAs($user);

        $task = Task::create([
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->user->id,
            'creator_id' => $user->id,
            'status_id' => Statuses::Doing->value,
        ]);

        $input = [
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $user->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::ToDo->value,
        ];

        $response = $this->putJson("$this->url/{$task->id}", $input);

        $response->assertStatus(500)
            ->assertJson([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                    'error' => "Cannot update when it's doing or done",
                ],
            ]);
    }

    /** @test */
    public function cannot_update_todo_status_if_the_previous_status_is_not_doing_or_canceled()
    {

        $task = Task::create([
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::Done->value,
        ]);

        $input = [
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::ToDo->value,
        ];

        $response = $this->patchJson("$this->url/{$task->id}/status", $input);

        $response->assertStatus(500)
            ->assertJson([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                    'error' => 'can only be reused if the previous status was doing or canceled',
                ],
            ]);
    }

    /** @test */
    public function cannot_update_todo_status_if_report_has_been_filled()
    {
        $task = Task::create([
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::Done->value,
            'report' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
        ]);

        $input = [
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::ToDo->value,
        ];

        $response = $this->patchJson("$this->url/{$task->id}/status", $input);

        $response->assertStatus(500)
            ->assertJson([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                    'error' => 'can only be reused if the report has not been filled',
                ],
            ]);
    }

    /** @test */
    public function cannot_update_doing_status_if_previous_status_is_not_to_do()
    {
        $task = Task::create([
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::Canceled->value,
        ]);

        $input = [
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::Doing->value,
        ];

        $response = $this->patchJson("$this->url/{$task->id}/status", $input);

        $response->assertStatus(500)
            ->assertJson([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                    'error' => 'can only be used if the previous status is doing',
                ],
            ]);
    }

    /** @test */
    public function cannot_update_done_status_if_previous_status_is_not_doing()
    {
        $task = Task::create([
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::Canceled->value,
        ]);

        $input = [
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::Done->value,
        ];

        $response = $this->patchJson("$this->url/{$task->id}/status", $input);

        $response->assertStatus(500)
            ->assertJson([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                    'error' => 'can only mark as done from doing',
                ],
            ]);
    }

    /** @test */
    public function cannot_update_canceled_status_if_previous_status_is_not_to_do_or_doing()
    {
        $task = Task::create([
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::Done->value,
        ]);

        $input = [
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::Canceled->value,
        ];

        $response = $this->patchJson("$this->url/{$task->id}/status", $input);

        $response->assertStatus(500)
            ->assertJson([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                    'error' => 'can only be used if the previous status is To Do or doing',
                ],
            ]);
    }

    /** @test */
    public function cannot_update_canceled_status_if_report_not_filled_or_null()
    {
        $task = Task::create([
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::Doing->value,
            'report' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
        ]);

        $input = [
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::Canceled->value,
        ];

        $response = $this->patchJson("$this->url/{$task->id}/status", $input);

        $response->assertStatus(500)
            ->assertJson([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                    'error' => 'can only be used if the report has not been filled in',
                ],
            ]);
    }

    /** @test */
    public function cannot_update_canceled_status_if_not_creator()
    {

        $task = Task::create([
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::Doing->value,
        ]);

        $input = [
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->assignee->id,
            'status_id' => Statuses::Canceled->value,
        ];

        $response = $this->patchJson("$this->url/{$task->id}/status", $input);

        $response->assertStatus(500)
            ->assertJson([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                    'error' => 'can only be used by the task creator',
                ],
            ]);
    }

    public function cannot_update_report_if_status_is_not_doing()
    {

        $task = Task::create([
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::Doing->value,
        ]);

        $input = [
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::ToDo->value,
            'report' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
        ];

        $response = $this->patchJson("$this->url/{$task->id}/report", $input);

        $response->assertStatus(500)
            ->assertJson([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                    'error' => 'Can only be filled in when Doing status',
                ],
            ]);
    }

    public function cannot_update_report_if_not_creator()
    {

        $task = Task::create([
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->user->id,
            'status_id' => Statuses::Doing->value,
        ]);

        $input = [
            'title' => 'Cuci AC ruang kelas',
            'description' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
            'assignee_id' => $this->assignee->id,
            'creator_id' => $this->assignee->id,
            'status_id' => Statuses::ToDo->value,
            'report' => 'Cuci seluruh AC ruang kelas, dari lantai 1 sampai 10',
        ];

        $response = $this->patchJson("$this->url/{$task->id}/report", $input);

        $response->assertStatus(500)
            ->assertJson([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                    'error' => 'Can be filled in by the maker or implementer',
                ],
            ]);
    }
}
