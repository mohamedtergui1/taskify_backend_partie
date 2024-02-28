<?php

namespace Tests\Unit\Http\Controllers\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;

use Illuminate\Foundation\Testing\RefreshDatabase;


class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();  
    }

    public function test_index_returns_tasks_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        Task::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson(route('tasks.index'));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         '*' => [
                             'name',
                             'description',
                             'start_date',
                             'end_date',

                         ]
                     ]
                 ]);
    }

    public function test_store_creates_task_for_authenticated_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $taskData = Task::factory()->make()->toArray();

        $response = $this->postJson(route('tasks.store'), $taskData);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => true,
                     'message' => 'task created with success',
                 ]);
    }


    public function test_show_returns_task_if_exists_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson(route('tasks.show',  $task->id));

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'the task is found',
                 ]);
    }

    public function test_update_updates_task_if_exists_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $task = Task::factory()->create(['user_id' => $user->id]);
        $updatedData = ['name' => 'Updated Name', 'status' => 'completed'
        , "description" => "create new projrct for my life",
            "start_date" => "2024-02-29 09:00:00",
            "end_date" => "2024-03-26 09:00:00"];



        $response = $this->putJson(route('tasks.update',  $task->id), $updatedData);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'task updates success',
                 ]);
    }

    public function test_destroy_deletes_task_if_exists_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson(route('tasks.destroy',  $task->id));

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'task deleted with success',
                 ]);
    }
}
