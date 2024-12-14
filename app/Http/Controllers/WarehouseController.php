<?php

namespace App\Http\Controllers;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\WarehouseItem;
use App\Models\Item;

class WarehouseController extends Controller
{
    public function index(){
        $warehouses = Warehouse::orderBy('name')
                      ->paginate(10);

        return view('warehouses.index', compact('warehouses'));
    }

    public function warehouseEppStock(Warehouse $warehouse)
{
    $warehouseItems = WarehouseItem::with(['item.size'])
        ->whereHas('item', function($query) {
            $query->where('is_epp', true);
        })
        ->where('warehouse_id', $warehouse->id)
        ->orderBy('current_stock')
        ->get();

    return view('warehouses.epp-stock-detail', compact('warehouse', 'warehouseItems'));
}


public function eppStockDetail(Warehouse $warehouse)
{
    $warehouseItems = WarehouseItem::with(['item.size'])
        ->whereHas('item', function($query) {
            $query->where('is_epp', true);
        })
        ->where('warehouse_id', $warehouse->id)
        ->orderBy('current_stock')
        ->get();

    return view('warehouses.epp-stock-detail', compact('warehouse', 'warehouseItems'));
}



    public function create(){
        return view('warehouses.create');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'description' => 'nullable'
        ]);

        Warehouse::create($request->all());

        return redirect()->route('warehouses.index')
                         ->with('success', 'Deposito creado con éxito.');
    }

    public function edit(Warehouse $warehouse){
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse){
        $request->validate([
            'name' => 'required',
            'description' => 'nullable'
        ]);

        $warehouse->update($request->all());

        return redirect()->route('warehouses.index')
                         ->with('success', 'Warehouse updated successfully');
    }

    public function destroy(Warehouse $warehouse){
        $warehouse->delete();

        return redirect()->route('warehouses.index')
                         ->with('success', 'Deposito eliminado con éxito');
    }
}
