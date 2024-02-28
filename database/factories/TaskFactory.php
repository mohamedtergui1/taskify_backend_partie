<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'status' => $this->faker->randomElement(['to do', 'in progress', 'completed']),
            'description' => $this->faker->paragraph,
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
            'start_date' => $this->faker->dateTimeBetween('+1 day', '+1 week')->format('Y-m-d H:i:s'),
            'end_date' => $this->faker->dateTimeBetween('+2 week', '+3 weeks')->format('Y-m-d H:i:s')

        ];
    }
}
