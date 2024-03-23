<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
});

afterEach(function () {
    $this->user->delete();
});

it('registers a new user', function () {
    $user = User::where('email', 'aa@bb.com')->delete();

    $response = $this->postJson('/api/register', [
        'name' => 'AA',
        'email' => 'aa@bb.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'token',
        ]);
});

it('logs in an existing user', function () {
    $response = $this->postJson('/api/login', [
        'email' => $this->user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'token',
        ]);
});

it('logs out a user', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/logout', [
        'email' => $user->email,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Logged out successfully',
        ]);

    $this->assertNull(Auth::user());

    $user->delete();
});
