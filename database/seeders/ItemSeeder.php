<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertar EPPs
        DB::table('items')->insert([
            [
                'part_number' => 'EPP001',
                'name' => 'Epp Seed 1',
                'description' => 'Description of item 1',
                'category_id' => 1, // Asegúrate de que esta categoría existe
                'brand_id' => 1,    // Asegúrate de que esta marca existe
                'minimum_stock' => 10,
                'size_id' => 1,     // Asegúrate de que este talle existe
                'is_epp' => true,
                'requires_return' => true,
            ],
            [
                'part_number' => 'EPP002',
                'name' => 'Epp Seed 2',
                'description' => 'Description of item 2',
                'category_id' => 1,
                'brand_id' => 1,
                'minimum_stock' => 10,
                'size_id' => 1,
                'is_epp' => true,
                'requires_return' => false,
            ],
            [
                'part_number' => 'EPP003',
                'name' => 'Epp Seed 3',
                'description' => 'Description of item 3',
                'category_id' => 1,
                'brand_id' => 1,
                'minimum_stock' => 10,
                'size_id' => 2,
                'is_epp' => true,
                'requires_return' => true,
            ],
            [
                'part_number' => 'EPP004',
                'name' => 'Epp Seed 4',
                'description' => 'Description of item 4',
                'category_id' => 1,
                'brand_id' => 1,
                'minimum_stock' => 10,
                'size_id' => 2,
                'is_epp' => true,
                'requires_return' => false,
            ],
        ]);

        // Insertar otros ítems que no son EPP
        DB::table('items')->insert([
            [
                'part_number' => 'ITEM001',
                'name' => 'Item 1',
                'description' => 'Description of item 5',
                'category_id' => 2,
                'brand_id' => 2,
                'minimum_stock' => 10,
                'is_epp' => false,
            ],
            [
                'part_number' => 'ITEM002',
                'name' => 'Item 2',
                'description' => 'Description of item 6',
                'category_id' => 3,
                'brand_id' => 3,
                'minimum_stock' => 5,
                'is_epp' => false,
            ],
            [
                'part_number' => 'ITEM003',
                'name' => 'Item 3',
                'description' => 'Description of item 7',
                'category_id' => 4,
                'brand_id' => 4,
                'minimum_stock' => 15,
                'is_epp' => false,
            ],
        ]);
    }
}
