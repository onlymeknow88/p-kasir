<?php

use App\Http\Controllers\BarangController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisHargaController;
use App\Http\Controllers\Setting\MenuController;
use App\Http\Controllers\Setting\RoleController;
use App\Http\Controllers\SettingPajakController;
use App\Http\Controllers\SettingInvoiceController;
use App\Http\Controllers\Setting\SettingAppController;
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

    //barang
    Route::get('/barang/GenerateBarcodeNumber', [BarangController::class,'ajaxGenerateBarcodeNumber']);
    Route::resource('/barang', BarangController::class);


    //user
    Route::post('/check-username', [UserController::class,'checkUsername']);
    Route::post('/check-email', [UserController::class,'checkEmail']);
    Route::resource('/user', UserController::class);

    //setting group
    Route::resource('/jenis-harga', JenisHargaController::class);
    Route::resource('/invoice', SettingInvoiceController::class)->only('index','store');
    Route::resource('/pajak', SettingPajakController::class)->only('index','store');

    //supplier
    Route::resource('/supplier', SupplierController::class);
    //customer
    Route::resource('/customer', CustomerController::class);



    //refrensi group
    Route::resource('/unit', UnitController::class);
    Route::post('/kategori/u-kategori', [KategoriController::class, 'ajaxUpdateKategoriUrut']);
    Route::resource('/kategori', KategoriController::class);

    //mainmenu aplikasi
    Route::name('aplikasi.')->prefix('aplikasi')->group(function () {
        //menu kategori
        Route::post('/menuKategori/u-kategori', [MenuKategoriController::class, 'ajaxUpdateKategoriUrut']);
        Route::resource('/menuKategori', MenuKategoriController::class);
        //menu
        Route::post('/menu/u-menu', [MenuController::class, 'ajaxUpdateUrut']);
        Route::get('/menu/kategori/{id}', [MenuController::class, 'buildMenu']);
        Route::get('/menu/{id}/s-menu', [MenuController::class, 'showMenu']);
        Route::post('/menu/get-pMenu', [MenuController::class, 'getParent']);
        Route::resource('/menu', MenuController::class);
        //role
        Route::post('/role/get-menu', [RoleController::class, 'list_menu'])->name('getListMenu');
        Route::resource('/role', RoleController::class);

        //menu-role-permission
        // Route::resource('/menu-role', PermissionController::class);

        //submenu setting
        Route::name('setting.')->prefix('setting')->group(function () {
            // setting
            Route::post('/setting-app', [SettingAppController::class, 'store'])->name('setting-app.store');
            Route::get('/setting-app', [SettingAppController::class, 'index'])->name('setting-app.index');
        });
    });
});
