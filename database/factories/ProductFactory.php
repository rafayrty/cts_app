<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->title;

        return [
            'demo_name' => 'Dummy title {basmti}',
            'replace_name' => $this->faker->name,
            'product_name' => $title,
            'is_published' => true,
            'excerpt' => $this->faker->sentence,
            'slug' => Str::slug($title.uniqid()),
            'is_rtl' => true,
            'description' => $this->faker->randomHtml,
            'pdf_info' => $this->faker->randomHtml,
            'featured' => false,
            'languages' => false,
            'product_type' => 1,
            'sold_amount' => 0,
            'price' => '200',
            'images' => '[]',
        ];
    }
}
