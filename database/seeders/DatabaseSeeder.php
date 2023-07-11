<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(1)
            ->has(
                Task::factory()
                    ->has(
                        Task::factory()
                            ->count(2)
                            ->state(function (array $attributes, Task $task) {
                                return ['user_id' => $task->user_id];
                            }),
                        'subtasks'
                    )
                    ->count(2),
                'tasks'
            )
            ->create();

        User::factory(1)
            ->has(Task::factory()->count(2),
                'tasks'
            )
            ->create();
    }
}
