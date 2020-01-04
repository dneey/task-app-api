<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_register_user()
    {
        // $this->with
        $user = [
            'name' => 'amajohnsom' ??  $this->faker->name,
            'email' => 'test@email.com' ??  $this->faker->unique()->safeEmail,
            'password' => 'Password1',
            'password_confirmation' => 'Password1'
        ];
        $response = $this->post('/api/register', $user);
        $response->assertStatus(200);
    }
}
