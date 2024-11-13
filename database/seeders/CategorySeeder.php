<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('categories')->insert([
            'name' => 'Category 1',
            'description' => 'Description of category 1',
        ]);

        \DB::table('categories')->insert([
            'name' => 'Category 2',
            'description' => 'Description of category 2',
        ]);

        \DB::table('categories')->insert([
            'name' => 'Category 3',
            'description' => 'Description of category 3',
        ]);
    }
}
