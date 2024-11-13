<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('brands')->insert([
            'name' => 'Brand 1',
            'description' => 'Description of brand 1',
        ]);

        \DB::table('brands')->insert([
            'name' => 'Brand 2',
            'description' => 'Description of brand 2',
        ]);

        \DB::table('brands')->insert([
            'name' => 'Brand 3',
            'description' => 'Description of brand 3',
        ]);
        
    }
}
