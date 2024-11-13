<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('warehouses')->insert([
            'name' => 'Warehouse 1',
            'description' => 'Description of warehouse 1',
        ]);

        \DB::table('warehouses')->insert([
            'name' => 'Warehouse 2',
            'description' => 'Description of warehouse 2',
        ]);

        \DB::table('warehouses')->insert([
            'name' => 'Warehouse 3',
            'description' => 'Description of warehouse 3',
        ]);
    }
}
