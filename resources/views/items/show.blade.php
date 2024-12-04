@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                {{-- Encabezado con acciones --}}
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        Detalle del Producto: {{ $item->name }}
                    </h2>
                    <div class="flex space-x-3">
                        <a href="{{ route('items.edit', $item) }}" 
                           class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                            Editar
                        </a>
                        <a href="{{ route('items.index') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Volver
                        </a>
                    </div>
                </div>

                {{-- Información básica --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Información General</h3>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Número de Parte</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $item->part_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $item->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Descripción</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $item->description ?: 'Sin descripción' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Categoría</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $item->category->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Marca</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $item->brand->name }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Estado de Stock</h3>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Stock Total</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $item->total_stock }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Stock Mínimo</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $item->minimum_stock }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                <dd class="mt-1">
                                    @if($item->total_stock <= $item->minimum_stock)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Stock Bajo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Stock Normal
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Stock por Depósito --}}
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Stock por Depósito</h3>
                    <div class="bg-white shadow overflow-hidden sm:rounded-md">
                        <ul class="divide-y divide-gray-200">
                            @forelse($item->warehouseItems as $warehouseItem)
                                <li class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $warehouseItem->warehouse->name }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Stock actual: {{ $warehouseItem->current_stock }}
                                            </p>
                                        </div>
                                        <div class="ml-2">
                                            @if($warehouseItem->current_stock <= $item->minimum_stock)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Stock Bajo
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Stock Normal
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="px-4 py-4 sm:px-6 text-sm text-gray-500 italic">
                                    No hay stock registrado en ningún depósito
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                {{-- Historial de Movimientos --}}
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Historial de Movimientos</h3>
                    </div>
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipo
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cantidad
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Depósito
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Usuario
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Comentarios
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($movements as $movement)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $movement->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $typeClass = match($movement->type) {
                                                    'entry' => 'bg-green-100 text-green-800',
                                                    'exit' => 'bg-red-100 text-red-800',
                                                    default => 'bg-blue-100 text-blue-800'
                                                };
                                                $typeText = match($movement->type) {
                                                    'entry' => 'Entrada',
                                                    'exit' => 'Salida',
                                                    default => 'Transferencia'
                                                };
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $typeClass }}">
                                                {{ $typeText }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $movement->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($movement->type === 'transfer')
                                                <span class="font-medium text-gray-700">{{ $movement->sourceWarehouse->name }}</span>
                                                <span class="text-blue-500 mx-2">→</span>
                                                <span class="font-medium text-gray-700">{{ $movement->destinationWarehouse->name }}</span>
                                            @elseif($movement->type === 'entry')
                                                <span class="text-green-500 mr-2">→</span>
                                                <span class="font-medium text-gray-700">{{ $movement->destinationWarehouse->name }}</span>
                                            @else
                                                <span class="font-medium text-gray-700">{{ $movement->sourceWarehouse->name }}</span>
                                                <span class="text-red-500 ml-2">→</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $movement->user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ Str::limit($movement->comments, 50) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center italic">
                                            No hay movimientos registrados
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Paginación --}}
                        @if($movements->hasPages())
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                                {{ $movements->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection