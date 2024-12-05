<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Size;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        $eppCategory = Category::firstOrCreate(['name' => 'EPP']);
        $brands = Brand::pluck('id')->toArray();
        $sizes = Size::pluck('id')->toArray();

        return [
            'part_number' => $this->faker->unique()->regexify('EPP-[0-9]{3}'),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'category_id' => $eppCategory->id,
            'brand_id' => $this->faker->randomElement($brands),
            'minimum_stock' => $this->faker->numberBetween(5, 15),
            'is_epp' => true,
            'size_id' => $this->faker->randomElement(array_merge($sizes, [null])), // Posibilidad de tener null
            'requires_return' => $this->faker->boolean(),
        ];
    }
}
