<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/', [FilesController::class, 'create'])->name('create');
Route::post('/upload', [FilesController::class, 'upload'])->name('upload');
Route::post('/encrypt', [FilesController::class, 'encrypt'])->name('encrypt');
Route::post('/decrypt', [FilesController::class, 'decrypt'])->name('decrypt');
