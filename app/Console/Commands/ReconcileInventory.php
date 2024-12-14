<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use App\Models\Movement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReconcileInventory extends Command
{
    protected $signature = 'inventory:reconcile 
                          {warehouse? : ID del depósito específico}
                          {--fix : Corregir discrepancias automáticamente}
                          {--detail : Mostrar detalles de movimientos}';
    
    protected $description = 'Reconcilia el stock actual con los movimientos históricos';

    public function handle()
    {
        $this->info('Iniciando reconciliación de inventario...');
        
        $warehouses = $this->argument('warehouse') 
            ? Warehouse::where('id', $this->argument('warehouse'))->get()
            : Warehouse::all();

        $discrepancies = [];

        foreach ($warehouses as $warehouse) {
            $this->info("\n📦 Analizando depósito: {$warehouse->name}");
            
            // Obtener todos los items que tienen stock en este depósito
            $warehouseItems = WarehouseItem::where('warehouse_id', $warehouse->id)
                    ->with('item')
                    ->get();

            foreach ($warehouseItems as $warehouseItem) {
                $currentStock = $warehouseItem->current_stock;
                $movements = $this->analyzeMovements($warehouse->id, $warehouseItem->item_id);
                $expectedStock = $movements['final_balance'];

                // Si hay discrepancia
                if ($currentStock !== $expectedStock) {
                    $discrepancy = [
                        'warehouse' => $warehouse,
                        'item' => $warehouseItem->item,
                        'current_stock' => $currentStock,
                        'expected_stock' => $expectedStock,
                        'movements' => $movements['movements']
                    ];
                    
                    $discrepancies[] = $discrepancy;
                    $this->reportDiscrepancy($discrepancy);
                    
                    if ($this->option('fix')) {
                        $this->fixDiscrepancy($warehouse->id, $warehouseItem->item_id, $expectedStock);
                    }
                }
            }
        }

        $this->summarizeDiscrepancies($discrepancies);
    }

    private function analyzeMovements($warehouse_id, $item_id)
    {
        $movements = Movement::where(function($query) use ($warehouse_id) {
            $query->where('source_warehouse_id', $warehouse_id)
                  ->orWhere('destination_warehouse_id', $warehouse_id);
        })
        ->where('item_id', $item_id)
        ->orderBy('created_at', 'asc')
        ->get();

        $balance = 0;
        $movementLog = [];

        foreach ($movements as $movement) {
            $previousBalance = $balance;
            
            if ($movement->destination_warehouse_id == $warehouse_id) {
                $balance += $movement->quantity;
                $movementLog[] = [
                    'type' => 'Entrada',
                    'quantity' => $movement->quantity,
                    'previous_balance' => $previousBalance,
                    'new_balance' => $balance,
                    'date' => $movement->created_at,
                    'movement_id' => $movement->id
                ];
            }
            
            if ($movement->source_warehouse_id == $warehouse_id) {
                $balance -= $movement->quantity;
                $movementLog[] = [
                    'type' => 'Salida',
                    'quantity' => $movement->quantity,
                    'previous_balance' => $previousBalance,
                    'new_balance' => $balance,
                    'date' => $movement->created_at,
                    'movement_id' => $movement->id
                ];
            }
        }

        return [
            'final_balance' => $balance,
            'movements' => $movementLog
        ];
    }

    private function reportDiscrepancy($discrepancy)
    {
        $this->warn("\n⚠️ Discrepancia encontrada:");
        $this->table(
            ['Depósito', 'Producto', 'Stock Actual', 'Stock Esperado', 'Diferencia'],
            [[
                $discrepancy['warehouse']->name,
                $discrepancy['item']->name,
                $discrepancy['current_stock'],
                $discrepancy['expected_stock'],
                $discrepancy['current_stock'] - $discrepancy['expected_stock']
            ]]
        );

        if ($this->option('detail')) {
            $this->info("\nDetalle de movimientos:");
            $movementRows = [];
            foreach ($discrepancy['movements'] as $m) {
                $movementRows[] = [
                    $m['date'],
                    $m['type'],
                    $m['quantity'],
                    $m['previous_balance'],
                    $m['new_balance']
                ];
            }
            
            $this->table(
                ['Fecha', 'Tipo', 'Cantidad', 'Balance Anterior', 'Nuevo Balance'],
                $movementRows
            );
        }
    }

    private function fixDiscrepancy($warehouse_id, $item_id, $expectedStock)
    {
        try {
            WarehouseItem::where([
                'warehouse_id' => $warehouse_id,
                'item_id' => $item_id
            ])->update(['current_stock' => $expectedStock]);

            $this->info("✅ Stock corregido automáticamente");
            
            Log::info('Stock corregido automáticamente', [
                'warehouse_id' => $warehouse_id,
                'item_id' => $item_id,
                'new_stock' => $expectedStock
            ]);
        } catch (\Exception $e) {
            $this->error("Error al corregir el stock: " . $e->getMessage());
        }
    }

    private function summarizeDiscrepancies($discrepancies)
    {
        if (empty($discrepancies)) {
            $this->info("\n✅ No se encontraron discrepancias");
            return;
        }

        $this->error("\n❌ Se encontraron " . count($discrepancies) . " discrepancias");
        
        // Agrupar por depósito
        $byWarehouse = collect($discrepancies)->groupBy(function($d) {
            return $d['warehouse']->name;
        });

        $this->info("\n📊 Resumen por depósito:");
        foreach ($byWarehouse as $warehouse => $items) {
            $this->line(" - $warehouse: " . count($items) . " discrepancias");
        }

        // Guardar reporte detallado
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = storage_path("logs/reconciliation_{$timestamp}.json");
        file_put_contents($filename, json_encode($discrepancies, JSON_PRETTY_PRINT));
        
        $this->info("\n📄 Reporte detallado guardado en: $filename");
    }
}