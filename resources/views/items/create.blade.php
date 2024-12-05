@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Crear Nuevo Producto</h2>
                    <a href="{{ route('items.index') }}" 
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Volver
                    </a>
                </div>

                {{-- Mostrar errores de validación --}}
                @if ($errors->any())
                    <div class="mb-4">
                        <ul class="list-disc list-inside text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('items.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Número de Parte --}}
                        <div>
                            <label for="part_number" class="block text-sm font-medium text-gray-700">
                                Código de EPP o Número de Parte *
                            </label>
                            <input type="text" 
                                   name="part_number" 
                                   id="part_number" 
                                   value="{{ old('part_number') }}"
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
                                   value="{{ old('name') }}"
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
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
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
                                   value="{{ old('minimum_stock', 0) }}"
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
                                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- EPP --}}
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Equipo de Protección Personal</h3>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="is_epp" 
                                       id="is_epp"
                                       value="1"
                                       class="toggle-fields h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                       data-target="#epp_fields"
                                       {{ old('is_epp') ? 'checked' : '' }}>
                                <label for="is_epp" class="ml-2 text-sm text-gray-700">
                                    Es un EPP
                                </label>
                            </div>
                        </div>

                        <div id="epp_fields" class="space-y-4 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Talle --}}
                                <div>
                                    <label for="size_id" class="block text-sm font-medium text-gray-700">
                                        Talle
                                    </label>
                                    <select name="size_id" 
                                            id="size_id"
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Seleccionar talle</option>
                                        @foreach($sizes as $size)
                                            <option value="{{ $size->id }}" {{ old('size_id') == $size->id ? 'selected' : '' }}>
                                                {{ $size->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('size_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                   
                        </div>
                    </div>

                    {{-- Stock Inicial --}}
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Stock Inicial</h3>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="has_initial_stock" 
                                       id="has_initial_stock"
                                       value="1"
                                       class="toggle-fields h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                       data-target="#initial_stock_fields"
                                       {{ old('has_initial_stock') ? 'checked' : '' }}>
                                <label for="has_initial_stock" class="ml-2 text-sm text-gray-700">
                                    Agregar stock inicial
                                </label>
                            </div>
                        </div>

                        <div id="initial_stock_fields" class="space-y-4 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="warehouse_id" class="block text-sm font-medium text-gray-700">
                                        Depósito
                                    </label>
                                    <select name="warehouse_id" 
                                            id="warehouse_id"
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Seleccionar depósito</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('warehouse_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="initial_stock" class="block text-sm font-medium text-gray-700">
                                        Cantidad Inicial
                                    </label>
                                    <input type="number" 
                                           name="initial_stock" 
                                           id="initial_stock"
                                           value="{{ old('initial_stock', 0) }}"
                                           min="0"
                                           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('initial_stock')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
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
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Script para el toggle de campos
        $('.toggle-fields').change(function() {
            const targetId = $(this).data('target');
            const $target = $(targetId);

            if ($(this).is(':checked')) {
                $target.removeClass('hidden');
                $target.find('input, select').prop('required', true);
            } else {
                $target.addClass('hidden');
                $target.find('input, select').prop('required', false);
                $target.find('input[type="number"]').val('');
                $target.find('select').val('');
            }
        });

        // Ejecutar los cambios iniciales
        $('.toggle-fields').trigger('change');

        // Deshabilitar el botón de envío después de enviar el formulario
        $('form').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    });
</script>
@endpush