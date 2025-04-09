<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => [
                'zh' => $this->faker->sentence(6) . ' (中文)',
                'en' => $this->faker->sentence(6),
                'jp' => $this->faker->sentence(6) . ' (日本語)'
            ],
            'content' => [
                'zh' => $this->faker->paragraphs(3, true) . ' (中文內容)',
                'en' => $this->faker->paragraphs(3, true),
                'jp' => $this->faker->paragraphs(3, true) . ' (日本語の内容)'
            ],
            'author' => $this->faker->name(),
        ];
    }
}
