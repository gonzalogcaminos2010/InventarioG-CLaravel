<?php

namespace App\Http\Controllers;

use App\Models\EppDelivery;
use App\Models\Employee;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use App\Models\Movement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EppDeliveryController extends Controller
{
    public function index()
    {
        $deliveries = EppDelivery::with(['employee', 'user', 'warehouse'])
            ->latest()
            ->paginate(10);

        return view('epp-deliveries.index', compact('deliveries'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        $items = Item::where('is_epp', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();

        return view('epp-deliveries.create', compact('employees', 'items', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'delivery_date' => 'required|date',
            'comments' => 'nullable|string',
            'item_id' => 'required|array|min:1',
            'item_id.*' => 'required|exists:items,id',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function() use ($validated, $request) {
                // Crear la entrega
                $delivery = EppDelivery::create([
                    'employee_id' => $validated['employee_id'],
                    'user_id' => auth()->id(),
                    'warehouse_id' => $validated['warehouse_id'],
                    'delivery_date' => $validated['delivery_date'],
                    'status' => 'completed', // Cambiado a completed directamente
                    'comments' => $validated['comments']
                ]);

                // Crear los items de la entrega y registrar los movimientos
                foreach ($request->item_id as $key => $itemId) {
                    // Verificar stock disponible
                    $warehouseItem = WarehouseItem::where([
                        'warehouse_id' => $validated['warehouse_id'],
                        'item_id' => $itemId
                    ])->first();

                    if (!$warehouseItem || $warehouseItem->current_stock < $request->quantity[$key]) {
                        throw new \Exception("Stock insuficiente para el item seleccionado");
                    }

                    // Crear el item de entrega
                    $delivery->items()->create([
                        'item_id' => $itemId,
                        'quantity' => $request->quantity[$key]
                    ]);

                    // Actualizar stock
                    $warehouseItem->current_stock -= $request->quantity[$key];
                    $warehouseItem->save();

                    // Registrar movimiento
                    Movement::create([
                        'item_id' => $itemId,
                        'user_id' => auth()->id(),
                        'source_warehouse_id' => $validated['warehouse_id'],
                        'type' => 'exit',
                        'status' => 'completed',
                        'quantity' => $request->quantity[$key],
                        'comments' => "Entrega de EPP a " . Employee::find($validated['employee_id'])->name
                    ]);
                }
            });

            return redirect()->route('epp-deliveries.index')
                ->with('success', 'Entrega registrada exitosamente.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(EppDelivery $eppDelivery)
    {
        $eppDelivery->load(['employee', 'user', 'warehouse', 'items.item']);
        return view('epp-deliveries.show', compact('eppDelivery'));
    }
}