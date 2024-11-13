<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Movement;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Estadísticas básicas
        $totalItems = Item::count();
        $totalWarehouses = Warehouse::where('is_active', true)->count();
        $totalMovements = Movement::count();

        // Productos con stock bajo
        $lowStockItems = Item::whereHas('warehouseItems', function($query) {
            $query->whereRaw('current_stock <= minimum_stock');
        })->with(['warehouseItems.warehouse', 'category', 'brand'])
          ->take(5)
          ->get();

        // Últimos movimientos
        $recentMovements = Movement::with(['item', 'sourceWarehouse', 'destinationWarehouse', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Stock por almacén
        $stockByWarehouse = Warehouse::withSum('warehouseItems', 'current_stock')
            ->where('is_active', true)
            ->get();

        // Datos para el gráfico de movimientos por tipo
        $movementsByType = Movement::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();

        // Datos para el gráfico de stock por almacén
        $stockData = Warehouse::select('name', DB::raw('coalesce(sum(wi.current_stock), 0) as total_stock'))
            ->leftJoin('warehouse_items as wi', 'warehouses.id', '=', 'wi.warehouse_id')
            ->where('is_active', true)
            ->groupBy('warehouses.id', 'warehouses.name')
            ->get();

        return view('dashboard', compact(
            'totalItems',
            'totalWarehouses',
            'totalMovements',
            'lowStockItems',
            'recentMovements',
            'stockByWarehouse',
            'movementsByType',
            'stockData'
        ));
    }
}