<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\EppDeliveryController;
use App\Http\Controllers\EmployeeController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

    
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    
    Route::get('/epps', [ItemController::class, 'eppIndex'])->name('items.epp_index');
    Route::get('kardex/stock-report', [KardexController::class, 'stockReport'])->name('kardex.stock-report');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    //Ruta para kardex
Route::resource('kardex', KardexController::class);
//Rutas para categories
Route::resource('categories', CategoryController::class);

    // Rutas para items
Route::resource('items', ItemController::class);
    // Nueva ruta para ajuste de stock
    Route::put('items/{item}/adjust-stock', [ItemController::class, 'adjustStock'])
        ->name('items.adjust-stock');
        
//Rutas para brands
Route::resource('brands', BrandController::class);

//Rutas para entregas de EPP
// Rutas para la gestión de entregas de EPP
Route::resource('epp-deliveries', EppDeliveryController::class);


//Rutas para los empleados

Route::resource('employees', EmployeeController::class);

//Ruta para warehouses
Route::resource('warehouses', WarehouseController::class);

//Rutas para backups

Route::group(['middleware' => ['auth']], function () {
    
});

});



require __DIR__.'/auth.php';
