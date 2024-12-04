@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-7xl mx-auto">
        {{-- Encabezado con botón de volver --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Detalle del Movimiento</h2>
            <a href="{{ route('kardex.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Volver
            </a>
        </div>

        {{-- Información general del movimiento --}}
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Información del Movimiento
                </h3>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Tipo de Movimiento</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 
                                {{ $movement->type === 'entry' ? 'bg-green-100 text-green-800' : 
                                   ($movement->type === 'exit' ? 'bg-red-100 text-red-800' : 
                                    'bg-blue-100 text-blue-800') }}">
                                {{ $movement->type === 'entry' ? 'Entrada' : 
                                   ($movement->type === 'exit' ? 'Salida' : 'Transferencia') }}
                            </span>
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Fecha y Hora</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            {{ optional($movement->created_at)->format('d/m/Y H:i:s') }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Usuario</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            {{ optional($movement->user)->name ?? 'N/A' }}
                        </dd>
                    </div>
                    @if($movement->sourceWarehouse)
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Depósito de Origen</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                {{ $movement->sourceWarehouse->name }}
                            </dd>
                        </div>
                    @endif
                    @if($movement->destinationWarehouse)
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Depósito de Destino</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                {{ $movement->destinationWarehouse->name }}
                            </dd>
                        </div>
                    @endif
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Comentarios</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            {{ $movement->comments ?: 'Sin comentarios' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Tabla de items del movimiento --}}
        @if($relatedMovements->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Items del Movimiento
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Código
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Producto
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Categoría
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Marca
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cantidad
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($relatedMovements as $relatedMovement)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ optional($relatedMovement->item)->part_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ optional($relatedMovement->item)->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ optional(optional($relatedMovement->item)->category)->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ optional(optional($relatedMovement->item)->brand)->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $relatedMovement->quantity }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <p class="text-gray-500 text-sm">No se encontraron items relacionados con este movimiento.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection