<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\AuthController;

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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



Route::middleware(['auth'])->group(function () {
    Route::post('patients/import', [PatientController::class, 'import'])->name('patients.import');
    Route::resource('patients', PatientController::class)->except(['create', 'show', 'edit']);
    Route::post('laboratories/import', [LaboratoryController::class, 'import'])->name('laboratories.import');
    Route::get('laboratories/print-block', [LaboratoryController::class, 'printBlock'])->name('laboratories.print_block');
    Route::resource('laboratories', LaboratoryController::class);
});
