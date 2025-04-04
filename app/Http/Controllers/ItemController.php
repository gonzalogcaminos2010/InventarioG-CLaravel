<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Warehouse;
use App\Models\Movement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Size;  // Agregamos el modelo Size

class ItemController extends Controller
{

    public function eppIndex()
{
    // Obtener solo los ítems que son EPP, junto con su talla
    $eppItems = Item::where('is_epp', true)
        ->with('size','warehouseItems') // Cargar la relación de talla
        ->get();

    // Retornar la vista con los datos
    return view('items.epp_index', compact('eppItems'));
}

//ESTE METODO ES PARA OBTENER LOS EPP QUE TIENEN STOCK POR DEPOSITO

public function eppStockIndex()
{
    // Obtenemos todos los EPP
    $eppItems = Item::where('is_epp', true)
        ->with('size', 'warehouseItems.warehouse') // Cargamos las tallas y la relación con warehouseItems y Warehouses
        ->get();

    // Obtenemos todos los depósitos activos (o todos, según prefieras)
    $warehouses = Warehouse::orderBy('name')->get();

    // Retornamos la vista con la información necesaria
    return view('items.epp_stock_index', compact('eppItems', 'warehouses'));
}

    public function index(Request $request)
{
    
    $query = Item::with(['category', 'brand', 'warehouseItems']);

    // Búsqueda por texto
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('part_number', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%");
        });
    }

    // Filtro por categoría
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    // Filtro por marca
    if ($request->filled('brand')) {
        $query->where('brand_id', $request->brand);
    }

    // Filtro por estado de stock
    if ($request->filled('stock_status')) {
        $query->whereHas('warehouseItems', function($q) use ($request) {
            if ($request->stock_status === 'low') {
                $q->whereRaw('current_stock <= items.minimum_stock');
            } else {
                $q->whereRaw('current_stock > items.minimum_stock');
            }
        });
    }

    // Obtener categorías y marcas para los filtros
    $categories = Category::orderBy('name')->get();
    $brands = Brand::orderBy('name')->get();

    // Obtener resultados paginados
    $items = $query->orderBy('name')->paginate(10)
                   ->withQueryString(); // Mantener los parámetros de filtro en la paginación

    return view('items.index', compact('items', 'categories', 'brands'));
}

public function __construct()
{
    $this->middleware('auth');
}

public function create()
{
    $categories = Category::orderBy('name')->get();
    $brands = Brand::orderBy('name')->get();
    $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
    $sizes = Size::orderBy('name')->get();  // Agregamos los talles

    return view('items.create', compact('categories', 'brands', 'warehouses', 'sizes'));
}

public function store(Request $request)
{
    // Log para ver qué datos llegan
    \Log::info('Request data:', $request->all());

    try {
        $validated = $request->validate([
            'part_number' => 'required|unique:items',
            'name' => 'required',
            'description' => 'nullable',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'minimum_stock' => 'required|integer|min:0',
            'initial_stock' => 'nullable|integer|min:0',
            'warehouse_id' => 'required_with:initial_stock|exists:warehouses,id',
            'is_epp' => 'sometimes|accepted',
            'size_id' => 'required_if:is_epp,1|exists:sizes,id|nullable'
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Errores de validación:', $e->errors());
        return back()->withErrors($e->errors())->withInput();
    }

    \Log::info('Validated data:', $validated);

    try {
        $item = \DB::transaction(function() use ($validated, $request) {
            \Log::info('Creating item with:', [
                'part_number' => $validated['part_number'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'],
                'minimum_stock' => $validated['minimum_stock'],
                'is_epp' => $request->has('is_epp'),
                'size_id' => $request->size_id,
                'requires_return' => $request->has('requires_return'),
            ]);

            // Crear el item
            $item = Item::create([
                'part_number' => $validated['part_number'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'],
                'minimum_stock' => $validated['minimum_stock'],
                'is_epp' => $request->has('is_epp'),
                'size_id' => $request->size_id,
                'requires_return' => $request->has('requires_return'),
            ]);

            // Si hay stock inicial, crear el warehouse_item y el movimiento
            if ($request->filled('initial_stock') && $request->filled('warehouse_id')) {
                // Crear el registro en warehouse_items
                $item->warehouseItems()->create([
                    'warehouse_id' => $request->warehouse_id,
                    'current_stock' => $request->initial_stock
                ]);

                // Crear el movimiento inicial
                Movement::create([
                    'item_id' => $item->id,
                    'user_id' => auth()->id(),
                    'destination_warehouse_id' => $request->warehouse_id,
                    'type' => 'entry',
                    'status' => 'completed',
                    'quantity' => $request->initial_stock,
                    'comments' => 'Stock Inicial'
                ]);

                \Log::info('Initial stock movement created:', [
                    'item_id' => $item->id,
                    'warehouse_id' => $request->warehouse_id,
                    'quantity' => $request->initial_stock
                ]);
            }

            return $item;
        });

        return redirect()->route('items.index')
            ->with('success', 'Producto creado exitosamente.');

    } catch (\Exception $e) {
        \Log::error('Error creating item:', ['error' => $e->getMessage()]);
        return back()
            ->withInput()
            ->withErrors(['error' => 'Error al crear el item: ' . $e->getMessage()]);
    }
}
    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();

        return view('items.edit', compact('item', 'categories', 'brands', 'warehouses'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'part_number' => 'required|unique:items,part_number,' . $item->id,
            'name' => 'required',
            'description' => 'nullable',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'minimum_stock' => 'required|integer|min:0',
        ]);

        $item->update($validated);

        return redirect()->route('items.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function adjustStock(Request $request, Item $item)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'stock_adjustment' => 'required|integer|not_in:0',
            'adjustment_reason' => 'required|string'
        ]);
    
        \DB::transaction(function() use ($item, $validated, $request) {  // Nota el cambio aquí: \DB en lugar de DB
            $warehouseItem = $item->warehouseItems()
                ->firstOrCreate(
                    ['warehouse_id' => $request->warehouse_id],
                    ['current_stock' => 0]
                );
    
            // Actualizar el stock
            $warehouseItem->current_stock += $validated['stock_adjustment'];
            $warehouseItem->save();
    
            // Registrar el movimiento
            Movement::create([
                'item_id' => $item->id,
                'user_id' => auth()->id(),
                'source_warehouse_id' => $request->stock_adjustment < 0 ? $request->warehouse_id : null,
                'destination_warehouse_id' => $request->stock_adjustment > 0 ? $request->warehouse_id : null,
                'type' => $request->stock_adjustment > 0 ? 'entry' : 'exit',
                'status' => 'completed',
                'quantity' => abs($validated['stock_adjustment']),
                'comments' => $validated['adjustment_reason']
            ]);
        });
    
        return back()->with('success', 'Stock ajustado correctamente');
    }


    public function show(Item $item)
    {
        $movements = Movement::where('item_id', $item->id)
            ->with(['user', 'sourceWarehouse', 'destinationWarehouse'])
            ->latest()
            ->paginate(15);
    
        // Cargar la relación 'size' si no está cargada
        if (!$item->relationLoaded('size')) {
            $item->load('size');
        }
    
        return view('items.show', compact('item', 'movements'));
    }
    
    
public function destroy(Item $item)
{
    try {
        // Verificar si tiene stock
        $hasStock = $item->warehouseItems()
            ->where('current_stock', '>', 0)
            ->exists();

        if ($hasStock) {
            return back()->with('error', 
                'No se puede eliminar el producto porque tiene stock existente en uno o más depósitos. 
                Por favor, realice las salidas correspondientes antes de eliminar.');
        }

        // Verificar si tiene movimientos
        $hasMovements = $item->movements()->exists();

        if ($hasMovements) {
            return back()->with('error', 
                'No se puede eliminar el producto porque tiene movimientos históricos asociados. 
                Considere desactivarlo en su lugar.');
        }

        // Eliminar warehouse_items (registros con stock 0)
        $item->warehouseItems()->delete();
        
        // Eliminar el ítem
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Producto eliminado exitosamente.');

    } catch (\Exception $e) {
        return back()->with('error', 
            'Ocurrió un error al intentar eliminar el producto. ' . $e->getMessage());
    }
}
}