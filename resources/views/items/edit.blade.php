{{-- resources/views/items/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Editar Producto: {{ $item->name }}</h2>
                    <a href="{{ route('items.index') }}" 
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Volver
                    </a>
                </div>

                <form action="{{ route('items.update', $item) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Número de Parte --}}
                        <div>
                            <label for="part_number" class="block text-sm font-medium text-gray-700">
                                Número de Parte *
                            </label>
                            <input type="text" 
                                   name="part_number" 
                                   id="part_number" 
                                   value="{{ old('part_number', $item->part_number) }}"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   required>
                            @error('part_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nombre --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nombre *
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $item->name) }}"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Categoría --}}
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">
                                Categoría *
                            </label>
                            <select name="category_id" 
                                    id="category_id"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required>
                                <option value="">Seleccionar categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ (old('category_id', $item->category_id) == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Marca --}}
                        <div>
                            <label for="brand_id" class="block text-sm font-medium text-gray-700">
                                Marca *
                            </label>
                            <select name="brand_id" 
                                    id="brand_id"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required>
                                <option value="">Seleccionar marca</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" 
                                            {{ (old('brand_id', $item->brand_id) == $brand->id) ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('brand_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Stock Mínimo --}}
                        <div>
                            <label for="minimum_stock" class="block text-sm font-medium text-gray-700">
                                Stock Mínimo *
                            </label>
                            <input type="number" 
                                   name="minimum_stock" 
                                   id="minimum_stock" 
                                   value="{{ old('minimum_stock', $item->minimum_stock) }}"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   required>
                            @error('minimum_stock')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Descripción
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="3"
                                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('description', $item->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Stock por Depósito --}}
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Stock por Depósito</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($warehouses as $warehouse)
                                    @php
                                        $warehouseItem = $item->warehouseItems
                                            ->where('warehouse_id', $warehouse->id)
                                            ->first();
                                    @endphp
                                    <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $warehouse->name }}</p>
                                            <p class="text-sm text-gray-500">Stock Actual: {{ $warehouseItem ? $warehouseItem->current_stock : 0 }}</p>
                                        </div>
                                        <div>
                                            <a href="#" 
                                               class="text-indigo-600 hover:text-indigo-900"
                                               onclick="openStockModal('{{ $warehouse->name }}', {{ $warehouse->id }}, {{ $warehouseItem ? $warehouseItem->current_stock : 0 }})">
                                                Ajustar Stock
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('items.index') }}"
                           class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal para ajuste de stock --}}
<div id="stockModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Ajustar Stock</h3>
            <form id="adjustStockForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="modalWarehouseId" name="warehouse_id">
                
                <div>
                    <label for="stockAdjustment" class="block text-sm font-medium text-gray-700">
                        Cantidad a Ajustar
                    </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <input type="number" 
                               name="stock_adjustment" 
                               id="stockAdjustment"
                               class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300">
                    </div>
                </div>

                <div>
                    <label for="adjustmentReason" class="block text-sm font-medium text-gray-700">
                        Motivo del Ajuste
                    </label>
                    <textarea name="adjustment_reason" 
                              id="adjustmentReason"
                              rows="3"
                              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                </div>

                <div class="flex justify-end space-x-3 mt-4">
                    <button type="button"
                            onclick="closeStockModal()"
                            class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Guardar Ajuste
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openStockModal(warehouseName, warehouseId, currentStock) {
        const modal = document.getElementById('stockModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalWarehouseId = document.getElementById('modalWarehouseId');
        const form = document.getElementById('adjustStockForm');
        
        modalTitle.textContent = `Ajustar Stock - ${warehouseName}`;
        modalWarehouseId.value = warehouseId;
        form.action = `{{ route('items.adjust-stock', $item) }}`;
        
        modal.classList.remove('hidden');
    }

    function closeStockModal() {
        const modal = document.getElementById('stockModal');
        modal.classList.add('hidden');
    }

    // Cerrar modal si se hace clic fuera
    window.onclick = function(event) {
        const modal = document.getElementById('stockModal');
        if (event.target == modal) {
            closeStockModal();
        }
    }
</script>
@endpush
@endsection