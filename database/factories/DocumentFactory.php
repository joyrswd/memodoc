<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\ApiJob;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userId = User::factory()->create()->id;
        $apiJobId = ApiJob::factory(['user_id' => $userId])->create()->id;
        return [
            'user_id' => $userId,
            'api_job_id' => $apiJobId,
            'title' => fake()->text(50),
            'content' => fake()->text(140),
        ];
    }
}
