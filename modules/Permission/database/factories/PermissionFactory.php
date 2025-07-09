<?php

namespace Modules\Permission\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Permission\app\Models\Permission;

class PermissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'guard_name' => fake()->randomElement(['web', 'api']),
        ];
    }
}
