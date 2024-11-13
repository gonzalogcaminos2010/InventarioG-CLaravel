<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movement;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KardexController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function stockReport()
    {
        $warehouses = Warehouse::with(['warehouseItems.item'])
            ->where('is_active', true)
            ->get();

        return view('kardex.stock-report', [
            'warehouses' => $warehouses
        ]);
    }

    // ... resto de los métodos existentes ...
}