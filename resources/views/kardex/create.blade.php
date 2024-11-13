@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Crear Nuevo Movimiento</h2>
                    <a href="{{ route('kardex.index') }}" 
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Volver
                    </a>
                </div>

                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <strong>¡Error!</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('kardex.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Tipo de Movimiento --}}
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">
                            Tipo de Movimiento *
                        </label>
                        <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="">Seleccionar tipo de movimiento</option>
                            <option value="entrada" {{ old('type') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                            <option value="salida" {{ old('type') == 'salida' ? 'selected' : '' }}>Salida</option>
                            <option value="transfer" {{ old('type') == 'transfer' ? 'selected' : '' }}>Transferencia</option>
                        </select>
                    </div>

                    {{-- Depósitos --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div id="source_warehouse_div" class="hidden">
                            <label for="source_warehouse_id" class="block text-sm font-medium text-gray-700">
                                Depósito de Origen *
                            </label>
                            <select name="source_warehouse_id" id="source_warehouse_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Seleccionar depósito</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('source_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="destination_warehouse_div" class="hidden">
                            <label for="destination_warehouse_id" class="block text-sm font-medium text-gray-700">
                                Depósito de Destino *
                            </label>
                            <select name="destination_warehouse_id" id="destination_warehouse_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Seleccionar depósito</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('destination_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Tabla de Productos --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Productos *</label>
                        <table class="min-w-full divide-y divide-gray-200" id="products_table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            </tbody>
                        </table>
                        <button type="button" id="add_product" class="mt-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            + Agregar Producto
                        </button>
                    </div>

                    {{-- Comentarios --}}
                    <div>
                        <label for="comments" class="block text-sm font-medium text-gray-700">
                            Comentarios
                        </label>
                        <textarea name="comments" id="comments" rows="3" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('comments') }}</textarea>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('kardex.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const sourceWarehouseDiv = document.getElementById('source_warehouse_div');
    const destinationWarehouseDiv = document.getElementById('destination_warehouse_div');
    const addProductButton = document.getElementById('add_product');
    const productsTable = document.getElementById('products_table').getElementsByTagName('tbody')[0];
    let productCount = 0;

    // Manejar visibilidad de depósitos según tipo de movimiento
    typeSelect.addEventListener('change', function() {
        const type = this.value;
        
        if (type === 'entrada') {
            sourceWarehouseDiv.classList.add('hidden');
            destinationWarehouseDiv.classList.remove('hidden');
        } else if (type === 'salida') {
            sourceWarehouseDiv.classList.remove('hidden');
            destinationWarehouseDiv.classList.add('hidden');
        } else if (type === 'transfer') {
            sourceWarehouseDiv.classList.remove('hidden');
            destinationWarehouseDiv.classList.remove('hidden');
        } else {
            sourceWarehouseDiv.classList.add('hidden');
            destinationWarehouseDiv.classList.add('hidden');
        }
    });

    // Trigger initial state
    typeSelect.dispatchEvent(new Event('change'));

    // Agregar nueva fila de producto
    addProductButton.addEventListener('click', function() {
        const row = productsTable.insertRow();
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <select name="item_id[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    <option value="">Seleccionar producto</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="number" name="quantity[]" min="1" value="1"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
                <button type="button" class="text-red-600 hover:text-red-900 delete-row">
                    Eliminar
                </button>
            </td>
        `;
        productCount++;
    });

    // Eliminar fila de producto
    productsTable.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-row')) {
            e.target.closest('tr').remove();
            productCount--;
        }
    });

    // Agregar primera fila automáticamente
    addProductButton.click();
});
</script>
@endpush

@endsection