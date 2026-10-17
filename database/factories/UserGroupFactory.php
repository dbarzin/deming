<?php

namespace Database\Factories;

use App\Models\UserGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserGroupFactory extends Factory
{
    protected $model = UserGroup::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->sentence(),
            'type' => UserGroup::TYPE_BUSINESS,
        ];
    }

    public function security(): static
    {
        return $this->state(['type' => UserGroup::TYPE_SECURITY]);
    }
}
