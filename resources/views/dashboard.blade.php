@extends('layouts.app')

@section('content')
<div class="py-12 bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-6 mb-6 transition-transform transform hover:scale-[1.01]">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Panel de Control</h1>
                    <p class="mt-2 text-sm text-gray-700">Resumen general del sistema de inventario</p>
                </div>
                <div>
                    <img src="https://img.icons8.com/external-flat-wichaiwi/64/000000/external-dashboard-ecommerce-flat-wichaiwi.png" alt="Dashboard Icon" class="w-12 h-12">
                </div>
            </div>
        </div>

        <!-- Cards de Estadísticas -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Productos -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg transition-transform transform hover:scale-[1.03]">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 truncate uppercase">Total Productos</dt>
                            <dd class="mt-1 text-4xl font-extrabold text-indigo-600">{{ $totalItems }}</dd>
                        </div>
                        <div class="bg-indigo-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Almacenes -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg transition-transform transform hover:scale-[1.03]">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 truncate uppercase">Total Almacenes</dt>
                            <dd class="mt-1 text-4xl font-extrabold text-emerald-600">{{ $totalWarehouses }}</dd>
                        </div>
                        <div class="bg-emerald-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Movimientos -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg transition-transform transform hover:scale-[1.03]">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 truncate uppercase">Movimientos Totales</dt>
                            <dd class="mt-1 text-4xl font-extrabold text-blue-600">{{ $totalMovements }}</dd>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos en Alerta -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg transition-transform transform hover:scale-[1.03]">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 truncate uppercase">Productos en Alerta</dt>
                            <dd class="mt-1 text-4xl font-extrabold text-red-600">{{ $lowStockItems->count() }}</dd>
                        </div>
                        <div class="bg-red-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="mt-10 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Gráfico Movimientos -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-6 transition-transform transform hover:scale-[1.01]">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                    <img src="https://img.icons8.com/color/48/000000/pie-chart.png" class="w-6 h-6" alt="Chart Icon">
                    <span>Movimientos por Tipo</span>
                </h3>
                <div class="relative" style="height: 300px;">
                    <canvas id="movementsChart"></canvas>
                </div>
            </div>

            <!-- Gráfico Stock -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-6 transition-transform transform hover:scale-[1.01]">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                    <img src="https://img.icons8.com/color/48/000000/bar-chart.png" class="w-6 h-6" alt="Bar Chart Icon">
                    <span>Stock por Almacén</span>
                </h3>
                <div class="relative" style="height: 300px;">
                    <canvas id="stockChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tablas de Información -->
        <div class="mt-10 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Productos con Stock Bajo -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-6 transition-transform transform hover:scale-[1.01]">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                    <img src="https://img.icons8.com/color/48/000000/low-battery.png" class="w-6 h-6" alt="Low Stock Icon">
                    <span>Productos con Stock Bajo</span>
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Producto</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mínimo</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($lowStockItems as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $item->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            {{ $item->warehouseItems->sum('current_stock') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item->minimum_stock }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No hay productos con stock bajo
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Últimos Movimientos -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-6 transition-transform transform hover:scale-[1.01]">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                    <img src="https://img.icons8.com/color/48/000000/activity-history.png" class="w-6 h-6" alt="History Icon">
                    <span>Últimos Movimientos</span>
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                        <thead class="bg-purple-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipo</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Producto</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentMovements as $movement)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $movement->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $movement->type === 'entry' ? 'bg-green-100 text-green-800' : 
                                               ($movement->type === 'exit' ? 'bg-red-100 text-red-800' : 
                                                'bg-blue-100 text-blue-800') }}">
                                            {{ $movement->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $movement->item->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $movement->quantity }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No hay movimientos recientes
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gráfico de movimientos por tipo
        const movementsCtx = document.getElementById('movementsChart').getContext('2d');
        new Chart(movementsCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($movementsByType->pluck('type')) !!},
                datasets: [{
                    data: {!! json_encode($movementsByType->pluck('count')) !!},
                    backgroundColor: [
                        '#4f46e5', // entry
                        '#ef4444', // exit
                        '#10b981'  // transfer
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de stock por almacén
        const stockCtx = document.getElementById('stockChart').getContext('2d');
        new Chart(stockCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($stockData->pluck('name')) !!},
                datasets: [{
                    label: 'Stock Total',
                    data: {!! json_encode($stockData->pluck('total_stock')) !!},
                    backgroundColor: '#4f46e5'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                weight: 'bold'
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection