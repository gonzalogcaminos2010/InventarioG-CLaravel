@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Nueva Entrega de EPP</h2>
                    <a href="{{ route('epp-deliveries.index') }}" 
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

                <form action="{{ route('epp-deliveries.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Empleado --}}
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700">
                                Empleado *
                            </label>
                            <select name="employee_id" 
                                    id="employee_id"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required>
                                <option value="">Seleccionar empleado</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }} - {{ $employee->document_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Depósito --}}
                        <div>
                            <label for="warehouse_id" class="block text-sm font-medium text-gray-700">
                                Depósito *
                            </label>
                            <select name="warehouse_id" 
                                    id="warehouse_id"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required>
                                <option value="">Seleccionar depósito</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Fecha de Entrega --}}
                        <div>
                            <label for="delivery_date" class="block text-sm font-medium text-gray-700">
                                Fecha de Entrega *
                            </label>
                            <input type="date" 
                                   name="delivery_date" 
                                   id="delivery_date"
                                   value="{{ old('delivery_date', date('Y-m-d')) }}"
                                   class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   required>
                        </div>
                    </div>

                    {{-- Items a entregar --}}
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Items a Entregar *</label>
                        <table class="min-w-full divide-y divide-gray-200" id="items_table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EPP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CANTIDAD</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            </tbody>
                        </table>
                        <button type="button" 
                                id="add_item"
                                class="mt-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            + Agregar Item
                        </button>
                    </div>

                    {{-- Comentarios --}}
                    <div class="mt-6">
                        <label for="comments" class="block text-sm font-medium text-gray-700">
                            Comentarios
                        </label>
                        <textarea name="comments" 
                                  id="comments"
                                  rows="3"
                                  class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('comments') }}</textarea>
                    </div>

                    {{-- Botones de acción --}}
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('epp-deliveries.index') }}"
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsTable = document.getElementById('items_table').getElementsByTagName('tbody')[0];
    const addItemButton = document.getElementById('add_item');
    let itemCount = 0;

    // Función para agregar nueva fila
    addItemButton.addEventListener('click', function() {
        const row = itemsTable.insertRow();
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <select name="item_id[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    <option value="">Seleccionar EPP</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->name }} {{ $item->size ? '- Talle ' . $item->size->name : '' }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="number" 
                       name="quantity[]" 
                       min="1" 
                       value="1"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                       required>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
                <button type="button" class="text-red-600 hover:text-red-900 delete-row">
                    Eliminar
                </button>
            </td>
        `;
        itemCount++;
    });

    // Eliminar fila
    itemsTable.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-row')) {
            e.target.closest('tr').remove();
            itemCount--;
        }
    });

    // Agregar primera fila automáticamente
    addItemButton.click();
});
</script>
@endpush

@endsection