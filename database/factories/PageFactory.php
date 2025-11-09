<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition()
    {
        return [
            'page' => $this->faker->unique()->numberBetween(1, 1000),
            'text_content' => $this->faker->paragraph(3),
        ];
    }
}
