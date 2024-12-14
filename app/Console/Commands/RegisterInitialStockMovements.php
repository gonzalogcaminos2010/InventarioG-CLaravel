<?php

namespace App\Console\Commands;

use App\Models\Movement;
use App\Models\WarehouseItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegisterInitialStockMovements extends Command
{
    protected $signature = 'inventory:register-initial-stock';
    
    protected $description = 'Registra movimientos de stock inicial para items existentes';

    public function handle()
    {
        try {
            // Iniciar transacción manualmente
            DB::beginTransaction();

            $this->info('Eliminando movimientos existentes...');
            
            // Desactivar restricciones de clave foránea
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Eliminar registros de ambas tablas
            DB::table('movement_items')->truncate();
            DB::table('movements')->truncate();
            
            // Reactivar restricciones
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            $this->info('Registrando movimientos de stock inicial...');
            
            $warehouseItems = WarehouseItem::where('current_stock', '>', 0)->get();
            $count = 0;

            foreach ($warehouseItems as $warehouseItem) {
                Movement::create([
                    'item_id' => $warehouseItem->item_id,
                    'user_id' => 1,
                    'destination_warehouse_id' => $warehouseItem->warehouse_id,
                    'type' => 'entry',
                    'status' => 'completed',
                    'quantity' => $warehouseItem->current_stock,
                    'comments' => 'Stock Inicial (Corrección)',
                    'created_at' => $warehouseItem->created_at ?? now()
                ]);
                
                $count++;
                $this->info("Registrado stock inicial para item {$warehouseItem->item_id} en depósito {$warehouseItem->warehouse_id}");
            }

            // Confirmar la transacción
            DB::commit();

            $this->info("Proceso completado. Se registraron {$count} movimientos de stock inicial.");

        } catch (\Exception $e) {
            // Si algo falla, revertir todos los cambios
            DB::rollBack();
            $this->error("Error durante el proceso: " . $e->getMessage());
        }
    }
}