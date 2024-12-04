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
    // Mapeo de tipos de movimiento
    private const TYPE_MAP = [
        'entrada' => 'entry',
        'salida' => 'exit',
        'transferencia' => 'transfer'
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Movement::selectRaw('
            user_id,
            type,
            source_warehouse_id,
            destination_warehouse_id,
            comments,
            MIN(id) as first_id,
            COUNT(*) as items_count,
            created_at as operation_date,
            MIN(movements.id) as id
        ')
        ->with(['user', 'sourceWarehouse', 'destinationWarehouse']);

        // Filtro por producto
        if ($request->filled('item')) {
            $query->where('item_id', $request->item);
        }

        // Filtro por tipo de movimiento
        if ($request->filled('movement_type')) {
            $query->where('type', self::TYPE_MAP[$request->movement_type]);
        }

        // Filtro por rango de fechas
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->groupBy(
            'user_id',
            'type',
            'source_warehouse_id',
            'destination_warehouse_id',
            'comments',
            'created_at'
        )
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        $items = Item::orderBy('name')->get();

        return view('kardex.index', compact('movements', 'items'));
    }

    public function create()
    {
        $items = Item::with(['warehouseItems.warehouse', 'category', 'brand'])
                    ->orderBy('name')
                    ->get();
                    
        $warehouses = Warehouse::where('is_active', true)
                             ->orderBy('name')
                             ->get();

        return view('kardex.create', compact('items', 'warehouses'));
    }

    public function store(Request $request)
    {
        // Preparar reglas de validación base
        $rules = [
            'type' => 'required|in:entrada,salida,transferencia',
            'item_id' => 'required|array|min:1',
            'item_id.*' => 'required|exists:items,id',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|integer|min:1',
            'comments' => 'nullable|string'
        ];

        // Agregar reglas específicas según el tipo de movimiento
        switch ($request->type) {
            case 'entrada':
                $rules['destination_warehouse_id'] = 'required|exists:warehouses,id';
                break;
            case 'salida':
                $rules['source_warehouse_id'] = 'required|exists:warehouses,id';
                break;
            case 'transferencia':
                $rules['source_warehouse_id'] = 'required|exists:warehouses,id';
                $rules['destination_warehouse_id'] = 'required|exists:warehouses,id|different:source_warehouse_id';
                break;
        }

        // Validar la solicitud
        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            // Procesar cada producto
            foreach ($request->item_id as $key => $itemId) {
                $item = Item::findOrFail($itemId);
                $quantity = $request->quantity[$key];

                // Verificar stock disponible para salidas y transferencias
                if (in_array($request->type, ['salida', 'transferencia'])) {
                    $sourceWarehouseItem = WarehouseItem::firstOrNew([
                        'warehouse_id' => $request->source_warehouse_id,
                        'item_id' => $itemId
                    ], ['current_stock' => 0]);

                    if ($sourceWarehouseItem->current_stock < $quantity) {
                        throw new \Exception("Stock insuficiente para el producto {$item->name} en el depósito de origen.");
                    }
                }

                // Convertir el tipo de movimiento al valor del enum
                $movementType = self::TYPE_MAP[$request->type];

                // Crear el movimiento
                $movement = Movement::create([
                    'item_id' => $itemId,
                    'user_id' => Auth::id(),
                    'source_warehouse_id' => in_array($request->type, ['salida', 'transferencia']) ? $request->source_warehouse_id : null,
                    'destination_warehouse_id' => in_array($request->type, ['entrada', 'transferencia']) ? $request->destination_warehouse_id : null,
                    'type' => $movementType,
                    'status' => 'completed',
                    'quantity' => $quantity,
                    'comments' => $request->comments
                ]);

                // Actualizar stock según el tipo de movimiento
                switch ($request->type) {
                    case 'entrada':
                        $this->processEntryMovement($movement);
                        break;
                    case 'salida':
                        $this->processExitMovement($movement);
                        break;
                    case 'transferencia':
                        $this->processTransferMovement($movement);
                        break;
                }
            }

            DB::commit();
            return redirect()->route('kardex.index')->with('success', 'Movimientos registrados exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        // Cargar el movimiento principal con todas sus relaciones
        $movement = Movement::with([
            'user',
            'sourceWarehouse',
            'destinationWarehouse',
            'item.category',
            'item.brand'
        ])->findOrFail($id);

        // Cargar todos los movimientos relacionados con la misma operación
        $relatedMovements = Movement::with([
            'item.category',
            'item.brand',
            'sourceWarehouse',
            'destinationWarehouse'
        ])
        ->where('created_at', $movement->created_at)
        ->where('user_id', $movement->user_id)
        ->where('type', $movement->type)
        ->where('source_warehouse_id', $movement->source_warehouse_id)
        ->where('destination_warehouse_id', $movement->destination_warehouse_id)
        ->get();

        return view('kardex.show', compact('movement', 'relatedMovements'));
    }

    public function edit(Movement $movement)
    {
        return redirect()->route('kardex.index')
            ->with('error', 'Los movimientos no pueden ser editados una vez creados.');
    }

    public function update(Request $request, Movement $movement)
    {
        return redirect()->route('kardex.index')
            ->with('error', 'Los movimientos no pueden ser editados una vez creados.');
    }

    public function destroy(Movement $movement)
    {
        return redirect()->route('kardex.index')
            ->with('error', 'Los movimientos no pueden ser eliminados.');
    }

    private function processEntryMovement(Movement $movement)
    {
        $warehouseItem = WarehouseItem::firstOrNew([
            'warehouse_id' => $movement->destination_warehouse_id,
            'item_id' => $movement->item_id
        ]);

        $warehouseItem->current_stock = ($warehouseItem->current_stock ?? 0) + $movement->quantity;
        $warehouseItem->save();
    }

    private function processExitMovement(Movement $movement)
    {
        $warehouseItem = WarehouseItem::where([
            'warehouse_id' => $movement->source_warehouse_id,
            'item_id' => $movement->item_id
        ])->first();

        if (!$warehouseItem) {
            throw new \Exception('No se encontró stock en el depósito de origen.');
        }

        $warehouseItem->current_stock -= $movement->quantity;
        if ($warehouseItem->current_stock < 0) {
            throw new \Exception('Stock insuficiente en el depósito de origen.');
        }
        $warehouseItem->save();
    }

    private function processTransferMovement(Movement $movement)
    {
        // Reducir del origen
        $sourceWarehouseItem = WarehouseItem::where([
            'warehouse_id' => $movement->source_warehouse_id,
            'item_id' => $movement->item_id
        ])->first();

        if (!$sourceWarehouseItem) {
            throw new \Exception('No se encontró stock en el depósito de origen.');
        }

        $sourceWarehouseItem->current_stock -= $movement->quantity;
        if ($sourceWarehouseItem->current_stock < 0) {
            throw new \Exception('Stock insuficiente en el depósito de origen.');
        }
        $sourceWarehouseItem->save();

        // Aumentar en destino
        $destinationWarehouseItem = WarehouseItem::firstOrNew([
            'warehouse_id' => $movement->destination_warehouse_id,
            'item_id' => $movement->item_id
        ]);

        $destinationWarehouseItem->current_stock = ($destinationWarehouseItem->current_stock ?? 0) + $movement->quantity;
        $destinationWarehouseItem->save();
    }
}