<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\Auth\AuthController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function createUser()
    {
        return User::factory()->create([
            'password' => Hash::make('password'),
        ]);
    }

    public function testUserRegistration()
    {
        $controller = new AuthController(new \App\Repositories\UserRepository());

        $request = Request::create('/register', 'POST', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
        ]);

        $response = $controller->register($request);

        $this->assertEquals(201, $response->status());
        $this->assertArrayHasKey('status', $response->getData(true));
        $this->assertTrue($response->getData(true)['status']);
    }

    public function testUserLogin()
    {
        $user = $this->createUser();

        $controller = new AuthController(new \App\Repositories\UserRepository());

        $request = Request::create('/login', 'POST', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response = $controller->login($request);

        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('status', $response->getData(true));
        $this->assertTrue($response->getData(true)['status']);
    }

    public function testUserLogout()
    {
        $user = $this->createUser();

        $this->actingAs($user);

        $controller = new AuthController(new \App\Repositories\UserRepository());

        $request = Request::create('/logout', 'POST');

        $response = $controller->logout($request);

        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('status', $response->getData(true));
        $this->assertTrue($response->getData(true)['status']);
    }
}
