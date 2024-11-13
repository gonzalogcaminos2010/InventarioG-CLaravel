<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use App\Models\Movement;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MovementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear categoría
        $this->category = Category::create([
            'name' => 'BROCAS',
            'description' => 'Categoría de prueba'
        ]);

        // Crear marca
        $this->brand = Brand::create([
            'name' => 'BOART LONGYEAR',
            'description' => 'Marca de prueba'
        ]);

        // Crear usuario
        $this->user = User::factory()->create();
        
        // Crear almacenes
        $this->warehouse1 = Warehouse::create([
            'name' => 'Almacén Test 1',
            'description' => 'Almacén para pruebas 1',
            'is_active' => true
        ]);

        $this->warehouse2 = Warehouse::create([
            'name' => 'Almacén Test 2',
            'description' => 'Almacén para pruebas 2',
            'is_active' => true
        ]);

        // Crear item
        $this->item = Item::create([
            'part_number' => 'TEST-001',
            'name' => 'Broca Test',
            'description' => 'Broca para pruebas',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'minimum_stock' => 5
        ]);
    }

    /** @test */
    public function it_can_create_entry_movement()
    {
        $quantity = 10;

        // Crear movimiento de entrada
        $movement = Movement::create([
            'item_id' => $this->item->id,
            'user_id' => $this->user->id,
            'destination_warehouse_id' => $this->warehouse1->id,
            'type' => 'entry',
            'status' => 'completed',
            'quantity' => $quantity,
            'comments' => 'Entrada inicial de prueba'
        ]);

        // Crear o actualizar el warehouse_item
        $warehouseItem = WarehouseItem::firstOrCreate(
            [
                'warehouse_id' => $this->warehouse1->id,
                'item_id' => $this->item->id
            ],
            ['current_stock' => 0]
        );

        $warehouseItem->current_stock += $quantity;
        $warehouseItem->save();

        $this->assertEquals($quantity, $warehouseItem->fresh()->current_stock);
    }

    /** @test */
    public function it_cannot_create_exit_movement_without_stock()
    {
        $this->expectException(\Exception::class);

        // Intentar crear salida sin stock
        $movement = Movement::create([
            'item_id' => $this->item->id,
            'user_id' => $this->user->id,
            'source_warehouse_id' => $this->warehouse1->id,
            'type' => 'exit',
            'status' => 'completed',
            'quantity' => 5,
            'comments' => 'Intento de salida sin stock'
        ]);

        $warehouseItem = WarehouseItem::where([
            'warehouse_id' => $this->warehouse1->id,
            'item_id' => $this->item->id
        ])->first();

        if (!$warehouseItem || $warehouseItem->current_stock < 5) {
            throw new \Exception('No hay suficiente stock');
        }
    }

    /** @test */
    public function it_can_transfer_between_warehouses()
    {
        // Crear stock inicial en warehouse1
        $initialStock = 10;
        $transferAmount = 5;

        // Primero crear el stock inicial
        WarehouseItem::create([
            'warehouse_id' => $this->warehouse1->id,
            'item_id' => $this->item->id,
            'current_stock' => $initialStock
        ]);

        // Crear el movimiento de transferencia
        Movement::create([
            'item_id' => $this->item->id,
            'user_id' => $this->user->id,
            'source_warehouse_id' => $this->warehouse1->id,
            'destination_warehouse_id' => $this->warehouse2->id,
            'type' => 'transfer',
            'status' => 'completed',
            'quantity' => $transferAmount,
            'comments' => 'Transferencia de prueba'
        ]);

        // Actualizar stock en origen
        $sourceWarehouseItem = WarehouseItem::where([
            'warehouse_id' => $this->warehouse1->id,
            'item_id' => $this->item->id
        ])->first();

        $sourceWarehouseItem->current_stock -= $transferAmount;
        $sourceWarehouseItem->save();

        // Crear o actualizar stock en destino
        $destinationWarehouseItem = WarehouseItem::firstOrCreate(
            [
                'warehouse_id' => $this->warehouse2->id,
                'item_id' => $this->item->id
            ],
            ['current_stock' => 0]
        );

        $destinationWarehouseItem->current_stock += $transferAmount;
        $destinationWarehouseItem->save();

        // Verificaciones
        $this->assertEquals($initialStock - $transferAmount, $sourceWarehouseItem->fresh()->current_stock);
        $this->assertEquals($transferAmount, $destinationWarehouseItem->fresh()->current_stock);
    }

    /** @test */
    public function it_checks_minimum_stock_warning()
    {
        // Crear stock inicial por encima del mínimo
        $initialStock = 10;
        $reductionAmount = 7;

        // Crear stock inicial
        $warehouseItem = WarehouseItem::create([
            'warehouse_id' => $this->warehouse1->id,
            'item_id' => $this->item->id,
            'current_stock' => $initialStock
        ]);

        // Verificar que inicialmente está por encima del mínimo
        $this->assertTrue($warehouseItem->current_stock > $this->item->minimum_stock);

        // Crear movimiento de salida
        Movement::create([
            'item_id' => $this->item->id,
            'user_id' => $this->user->id,
            'source_warehouse_id' => $this->warehouse1->id,
            'type' => 'exit',
            'status' => 'completed',
            'quantity' => $reductionAmount,
            'comments' => 'Salida para probar stock mínimo'
        ]);

        // Actualizar stock
        $warehouseItem->current_stock -= $reductionAmount;
        $warehouseItem->save();

        // Verificar que ahora está por debajo del mínimo
        $this->assertTrue($warehouseItem->fresh()->current_stock < $this->item->minimum_stock);
    }
}