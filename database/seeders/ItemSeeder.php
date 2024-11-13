<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('items')->insert([
            'part_number' => '123456',
            'name' => 'Item 1',
            'description' => 'Description of item 1',
            'category_id' => 1,
            'brand_id' => 1,
            'minimum_stock' => 10,
        ]);

        \DB::table('items')->insert([
            'part_number' => '654321',
            'name' => 'Item 2',
            'description' => 'Description of item 2',
            'category_id' => 2,
            'brand_id' => 2,
            'minimum_stock' => 5,
        ]);

        \DB::table('items')->insert([
            'part_number' => '987654',
            'name' => 'Item 3',
            'description' => 'Description of item 3',
            'category_id' => 3,
            'brand_id' => 3,
            'minimum_stock' => 15,
        ]);
    
    }
}
