<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Todo;
use App\User;

class TodoTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $structure = ['responseMessage', 'responseCode', 'data'];

    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_can_get_all_todos_paginated()
    {
        $this->withoutExceptionHandling();

        $response = $this->get('/api/todos');
        $response->assertJson($response->decodeResponseJson());
        $response->assertStatus(200);
    }

    public function test_a_user_can_create_a_todo()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create(['email' => 'yarteyd@gmail.com']);
        $data = [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph()
        ];
        $response = $this->actingAs($user, 'api')->post('api/todos', $data);
        $response->assertStatus(200)
            ->assertJsonStructure($this->structure)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('todos', $data);
    }

    public function test_cannot_create_a_todo_without_a_title()
    {
        $data = [
            // 'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph()
        ];
        $response = $this->post('api/todos', $data);
        $response->assertStatus(200)
            ->assertJsonStructure($this->structure)
            ->assertJsonFragment(['responseCode' => '400', 'title' => ['The title field is required.']]);

        $this->assertDatabaseMissing('todos', $data);
    }

    public function test_can_delete_a_todo()
    {
        $this->withoutExceptionHandling();

        $todo = factory(Todo::class)->create();
        $this->delete('/api/todos/' . $todo->id)
            ->assertStatus(200)
            ->assertJsonStructure($this->structure);

        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }

    public function test_can_mark_task_as_completed()
    {
        $this->withoutExceptionHandling();

        $todo = factory(Todo::class)->create();

        $newData = [
            'completed' => true,
            'id' => $todo->id
        ];
        $this->put('/api/todos/' . $todo->id, $newData)
            ->assertStatus(200)
            ->assertJsonStructure($this->structure);

        $this->assertDatabaseHas('todos', $newData);
    }
}
