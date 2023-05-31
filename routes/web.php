<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Setting\MenuController;
use App\Http\Controllers\Setting\RoleController;
use App\Http\Controllers\Setting\MenuKategoriController;

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

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::resource('/dashboard', DashboardController::class);


    Route::name('aplikasi.')->prefix('aplikasi')->group(function () {
        //menu kategori
        Route::resource('/menuKategori', MenuKategoriController::class);
        //menu
        Route::get('/menu/kategori/{id}', [MenuController::class,'buildMenu']);
        Route::get('/menu/{id}/s-menu', [MenuController::class,'showMenu']);
        Route::post('/menu/get-pMenu', [MenuController::class,'getParent']);
        Route::resource('/menu', MenuController::class);
        //role
        Route::resource('/role', RoleController::class);


    });


});
