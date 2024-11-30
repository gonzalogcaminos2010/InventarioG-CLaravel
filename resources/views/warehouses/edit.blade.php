@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Editar Almacén</h2>
                    <a href="{{ route('warehouses.index') }}" 
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

                <form action="{{ route('warehouses.update', $warehouse->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Nombre del Almacén --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Nombre del Almacén *
                        </label>
                        <input type="text" name="name" id="name" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                               value="{{ old('name', $warehouse->name) }}" required>
                    </div>

                    {{-- Ubicación --}}
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">
                            Ubicación *
                        </label>
                        <input type="text" name="location" id="location" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                               value="{{ old('location', $warehouse->location) }}" required>
                    </div>

                    {{-- Comentarios --}}
                    <div>
                        <label for="comments" class="block text-sm font-medium text-gray-700">
                            Comentarios
                        </label>
                        <textarea name="comments" id="comments" rows="3" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('comments', $warehouse->comments) }}</textarea>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('warehouses.index') }}" 
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
@endsection