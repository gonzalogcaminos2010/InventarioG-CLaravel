<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Size;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            ['name' => 'XS', 'description' => 'Extra Small'],
            ['name' => 'S', 'description' => 'Small'],
            ['name' => 'M', 'description' => 'Medium'],
            ['name' => 'L', 'description' => 'Large'],
            ['name' => 'XL', 'description' => 'Extra Large'],
            ['name' => 'XXL', 'description' => 'Double Extra Large'],
            ['name' => '35-36', 'description' => 'Calzado 35-36'],
            ['name' => '37-38', 'description' => 'Calzado 37-38'],
            ['name' => '39-40', 'description' => 'Calzado 39-40'],
            ['name' => '41-42', 'description' => 'Calzado 41-42'],
            ['name' => '43-44', 'description' => 'Calzado 43-44'],
            ['name' => '45-46', 'description' => 'Calzado 45-46'],
        ];

        foreach ($sizes as $size) {
            Size::create($size);
        }
    }
}
