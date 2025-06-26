<?php

use Modules\User\App\Models\User;

it('returns a paginated list of users in JSend format', function () {
    User::factory()->count(5)->create();

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
