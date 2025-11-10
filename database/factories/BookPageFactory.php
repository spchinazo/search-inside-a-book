<?php

namespace Database\Factories;

use App\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\BookPage>
 */
class BookPageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_id' => Book::factory()->create()->id,
            'page' => rand(1, 999),
            'content' => $this->faker->paragraph()
        ];
    }
}
