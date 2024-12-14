@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Stock de EPP por Depósito</h2>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número de Parte / Código</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Talla</th>
                        @foreach($warehouses as $warehouse)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $warehouse->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($eppItems as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->part_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->size ? $item->size->name : 'Sin Talla' }}
                            </td>
                            @foreach($warehouses as $warehouse)
                                @php
                                    // Encontramos el warehouseItem correspondiente a este depósito, si existe
                                    $warehouseItem = $item->warehouseItems->firstWhere('warehouse_id', $warehouse->id);
                                    $stock = $warehouseItem ? $warehouseItem->current_stock : 0;
                                @endphp
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $stock }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 3 + $warehouses->count() }}" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No hay EPPs registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection