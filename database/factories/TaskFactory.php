<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'status' => Task::STATUS_TODO,
            'priority' => $this->faker->numberBetween(1, 5),
            'title' => $this->faker->realText(30),
            'description' => $this->faker->realText(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            //'user_id' => User::factory(),
            //'parent_id' => Task::factory(),
        ];
    }
}
