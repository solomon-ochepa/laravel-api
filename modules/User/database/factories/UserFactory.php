<?php

namespace Modules\User\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\User\App\Models\User;

class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * The name of the factory's corresponding model.
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $male = (bool) random_int(0, 1); // [0:female, 1:male] - 50% chance of being female/male
        $name = [
            'first_name' => $male ? fake()->firstNameMale() : fake()->firstNameFemale(),
            'other_name' => $male ? fake()->firstNameMale() : fake()->firstNameFemale(),
            'last_name' => fake()->lastName(),
        ];
        $username = Str::lower("{$name['first_name']}_{$name['last_name']}"); // .Str::random();
        $email = "{$username}@example.com";

        return [
            'first_name' => $name['first_name'],
            'other_name' => $name['other_name'],
            'last_name' => $name['last_name'],
            'username' => $username,
            'phone' => fake()->phoneNumber(),
            'email' => $email,
            'email_verified_at' => null,
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
