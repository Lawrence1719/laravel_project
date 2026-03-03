<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Make sure registration works and logs an event.
     */
    public function test_registration_and_logging(): void
    {
        // tell the Log facade to expect an info call
        Log::shouldReceive('info')
            ->once()
            ->with('User registered', \Mockery::subset(['email' => 'test@example.com']));

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('user.email', 'test@example.com');
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    /**
     * Verify login returns success and logs the event.
     */
    public function test_login_and_logging(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123'),
        ]);

        Log::shouldReceive('info')
            ->once()
            ->with('User logged in', \Mockery::subset(['email' => $user->email]));

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertOk();
        $response->assertJsonPath('user.email', $user->email);
    }
}
