@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Título y botón de agregar producto --}}
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Productos</h1>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('items.create') }}"
               class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar Producto
            </a>
        </div>
    </div>

    {{-- Mensajes de éxito/error --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Formulario de filtros --}}
    <div class="mt-8">
        <form action="{{ route('items.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Búsqueda por texto --}}
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Buscar</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                           placeholder="Nombre o código">
                </div>

                {{-- Filtro por categoría --}}
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Categoría</label>
                    <select name="category" id="category"
                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <option value="">Todas</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro por marca --}}
                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-700">Marca</label>
                    <select name="brand" id="brand"
                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <option value="">Todas</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro por estado de stock --}}
                <div>
                    <label for="stock_status" class="block text-sm font-medium text-gray-700">Estado de Stock</label>
                    <select name="stock_status" id="stock_status"
                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <option value="">Todos</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Bajo</option>
                        <option value="normal" {{ request('stock_status') == 'normal' ? 'selected' : '' }}>Normal</option>
                    </select>
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="mt-4 flex justify-end space-x-2">
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Filtrar
                </button>
                <a href="{{ route('items.index') }}"
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    {{-- Tabla de productos --}}
    <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    @if($items->count())
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    {{-- Encabezados de la tabla --}}
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Código</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nombre</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Categoría</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Marca</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Stock Total</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 min-w-[200px]">
                                        <span class="sr-only">Acciones</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                {{-- Iteración de productos --}}
                                @foreach ($items as $item)
                                    <tr>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $item->part_number }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $item->name }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $item->category->name }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $item->brand->name }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <span class="px-2 py-1 {{ $item->total_stock <= $item->minimum_stock ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} rounded-full">
                                                {{ $item->total_stock }}
                                            </span>
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            <div class="flex justify-end space-x-3">
                                                {{-- Acciones --}}
                                                <a href="{{ route('items.show', $item) }}" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    <span class="px-2 py-1 bg-blue-100 rounded-md flex items-center">
                                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        Ver
                                                    </span>
                                                </a>
                                                
                                                <a href="{{ route('items.edit', $item) }}" 
                                                   class="text-yellow-600 hover:text-yellow-900">
                                                    <span class="px-2 py-1 bg-yellow-100 rounded-md flex items-center">
                                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        Editar
                                                    </span>
                                                </a>
    
                                                <button type="button" 
                                                        onclick="openDeleteModal({{ $item->id }})"
                                                        class="text-red-600 hover:text-red-900">
                                                    <span class="px-2 py-1 bg-red-100 rounded-md flex items-center">
                                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        Eliminar
                                                    </span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Paginación --}}
                        <div class="mt-4">
                            {{ $items->links() }}
                        </div>
                    @else
                        <div class="p-4">
                            <p class="text-sm text-gray-500">No se encontraron productos con los criterios de búsqueda.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Confirmación de Eliminación --}}
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Confirmar Eliminación</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    ¿Está seguro que desea eliminar este producto? Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="flex justify-end mt-4">
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                            onclick="closeDeleteModal()"
                            class="bg-gray-200 px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 mr-2">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="bg-red-600 px-4 py-2 rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openDeleteModal(itemId) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        form.action = `/items/${itemId}`;
        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
    }

    // Cerrar modal al hacer clic fuera
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal) {
            closeDeleteModal();
        }
    }
</script>
@endpush
@endsection
