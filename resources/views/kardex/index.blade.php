@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Título y botón de nuevo movimiento --}}
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Kardex de Movimientos</h1>
            <p class="mt-2 text-sm text-gray-700">Registro de todos los movimientos de inventario</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('kardex.create') }}"
               class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Nuevo Movimiento
            </a>
        </div>
    </div>

    {{-- Mensajes de éxito/error --}}
    @if (session('success'))
        <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Formulario de filtros --}}
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <form action="{{ route('kardex.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Búsqueda por producto --}}
                <div>
                    <label for="item" class="block text-sm font-medium text-gray-700">Producto</label>
                    <select name="item" id="item" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Todos los productos</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ request('item') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro por tipo de movimiento --}}
                <div>
                    <label for="movement_type" class="block text-sm font-medium text-gray-700">Tipo de Movimiento</label>
                    <select name="movement_type" id="movement_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Todos</option>
                        <option value="entrada" {{ request('movement_type') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                        <option value="salida" {{ request('movement_type') == 'salida' ? 'selected' : '' }}>Salida</option>
                        <option value="transfer" {{ request('movement_type') == 'transfer' ? 'selected' : '' }}>Transferencia</option>
                    </select>
                </div>

                {{-- Fecha desde --}}
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700">Fecha Desde</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                {{-- Fecha hasta --}}
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700">Fecha Hasta</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <div class="mt-4 flex justify-end space-x-2">
                <a href="{{ route('kardex.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Limpiar
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    {{-- Tabla de movimientos --}}
    <div class="mt-8">
        <div class="flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        @if($movements->count())
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Fecha</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Producto</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tipo</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Cantidad</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Origen</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Destino</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Usuario</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Comentarios</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($movements as $movement)
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900 sm:pl-6">
                                                {{ $movement->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ $movement->item->name ?? 'N/A' }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                                <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 
                                                    {{ $movement->type === 'entrada' ? 'bg-green-100 text-green-800' : 
                                                       ($movement->type === 'salida' ? 'bg-red-100 text-red-800' : 
                                                        'bg-blue-100 text-blue-800') }}">
                                                    {{ ucfirst($movement->type) }}
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ $movement->quantity }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ $movement->sourceWarehouse->name ?? 'N/A' }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ $movement->destinationWarehouse->name ?? 'N/A' }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ $movement->user->name ?? 'N/A' }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ Str::limit($movement->comments, 30) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-6">
                                <p class="text-gray-500 text-sm">No se encontraron movimientos</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($movements->count())
            <div class="mt-4">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection