<?php

use Modules\User\App\Models\User;

it('returns a list of users in JSend format', function () {
    User::factory(5)->create();

    $response = $this->getJson('/api/v1/users');
    $response->assertOk()
        ->assertJsonStructure([
            'status',
            'data' => [
                'users' => [
                    [
                        'id',
                        'first_name',
                        'other_name',
                        'last_name',
                        'username',
                        'phone',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'status' => 'success',
        ]);

    // Assert: users are present in the response
    expect($response->json('data.users.0'))->toHaveCount(11);
});

it('returns a specific user in JSend format', function () {
    $user = User::factory()->create();

    $response = $this->getJson("/api/v1/users/{$user->id}");
    $response->assertOk()
        ->assertJsonStructure([
            'status',
            'data' => [
                'user' => [
                    'id',
                    'first_name',
                    'other_name',
                    'last_name',
                    'username',
                    'phone',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ])
        ->assertJson([
            'status' => 'success',
        ]);

    // Assert: user `id` is present in the response
    expect($response->json('data.user.id'))->toBe($user->id);
});

it('returns error 404 when trying to get non-existent user', function () {
    $fake_user_id = 419;

    $response = $this->getJson("/api/v1/users/{$fake_user_id}");
    $response->assertNotFound()
        ->assertJson([
            'status' => 'fail',
            'data' => [
                'message' => 'User not found',
            ],
        ]);
});

it('deletes a user successfully', function () {
    $user = User::factory()->create();

    $response = $this->deleteJson("/api/v1/users/{$user->id}");
    $response->assertOk()
        ->assertJson([
            'status' => 'success',
            'data' => [
                'message' => 'User deleted successfully',
            ],
        ]);

    // Assert: user is soft deleted
    $this->assertSoftDeleted('users', ['id' => $user->id]);
});

it('returns error 404 when trying to delete non-existent user', function () {
    $fake_user_id = 419;

    $response = $this->deleteJson("/api/v1/users/{$fake_user_id}");
    $response->assertNotFound()
        ->assertJson([
            'status' => 'fail',
            'data' => [
                'message' => 'User not found',
            ],
        ]);
});
