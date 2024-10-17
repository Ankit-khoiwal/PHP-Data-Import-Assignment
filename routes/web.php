<?php

use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

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
    return view('admin.pages.dataImport.index');
})->name('admin.dashboard');



Route::get('/upload-document', [ImportController::class, 'uploadeDocument'])->name('uploadeDocument');
Route::post('/upload-csv', [ImportController::class, 'uploadCSVData'])->name('import.csv');
Route::get('/process-csv-data', [ImportController::class, 'processCSVData'])->name('processCSVData');
