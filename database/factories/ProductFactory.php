<?php

namespace Database\Factories;

use App\Modules\Products\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->words(3, true);
        return [
            'title' => ucfirst($title),
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(100, 999),
            'image' => null,
            'description' => $this->faker->paragraph(3),
            'unit_price' => $this->faker->randomFloat(2, 5, 1000),
            'date_available' => $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'stock' => $this->faker->numberBetween(0, 150),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
