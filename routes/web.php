<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TesController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\FilePickerController;
use App\Http\Controllers\JenisHargaController;
use App\Http\Controllers\BarcodeCetakController;
use App\Http\Controllers\Setting\MenuController;
use App\Http\Controllers\Setting\RoleController;
use App\Http\Controllers\SettingPajakController;
use App\Http\Controllers\PembelianReturController;
use App\Http\Controllers\PenjualanReturController;
use App\Http\Controllers\PenjualanTempoController;
use App\Http\Controllers\SettingInvoiceController;
use App\Http\Controllers\TransferBarangController;
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

Route::get('/tes', [TesController::class, 'index']);
Route::post('/barcode-scanner', [TesController::class, 'scan']);

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {

    Route::resource('/dashboard', DashboardController::class);

    //pembelian
    Route::prefix('daftar-pembelian')->name('daftar-pembelian.')->group(function () {
        Route::get('/ajaxGetBarangByBarcode', [PembelianController::class, 'ajaxGetBarangByBarcode']);
        Route::post('/getDataBarang', [PembelianController::class, 'getDataBarang']);
        Route::get('/getDataDTListBarang', [PembelianController::class, 'getDataDTListBarang']);
        Route::get('/{id}/edit', [PembelianController::class, 'edit'])->name('edit');
        Route::resource('/', PembelianController::class)->except(['edit']);
    });

    //pembelian
    Route::prefix('pembelian-retur')->name('pembelian-retur.')->group(function () {
        Route::get('/notaReturPdf', [PembelianReturController::class, 'notaReturPdf']);
        Route::post('/getDataInvoice', [PembelianReturController::class, 'getDataInvoice']);
        Route::get('/getDataDTListInvoice', [PembelianReturController::class, 'getDataDTListInvoice']);
        Route::get('/{id}/edit', [PembelianReturController::class, 'edit'])->name('edit');
        Route::delete('/destroy/{id}', [PembelianReturController::class, 'destroy'])->name('destroy');
        Route::resource('/', PembelianReturController::class)->except(['edit','destroy']);
    });

    Route::prefix('penjualan-list')->name('penjualan-list.')->group(function () {
        Route::match(['get', 'post'],'/getListCustomer', [PenjualanController::class, 'getListCustomer']);
        Route::match(['get', 'post'],'/getDataDTCustomer', [PenjualanController::class, 'getDataDTCustomer']);
        Route::match(['get', 'post'],'/getDataBarang', [PenjualanController::class, 'getDataBarang']);
        Route::match(['get', 'post'],'/getDataDTListBarang', [PenjualanController::class, 'getDataDTListBarang']);
        Route::match(['get', 'post'],'/ajaxGetBarangByBarcode', [PenjualanController::class, 'ajaxGetBarangByBarcode']);
        Route::get('/{id}/edit', [PenjualanController::class, 'edit'])->name('edit');
        Route::delete('/destroy/{id}', [PenjualanController::class, 'destroy'])->name('destroy');
        Route::resource('/', PenjualanController::class)->except(['edit','destroy']);
    });

    Route::prefix('penjualan-retur')->name('penjualan-retur.')->group(function () {
        Route::post('/getDataInvoice', [PenjualanReturController::class, 'getDataInvoice']);
        Route::get('/getDataDTListInvoice', [PenjualanReturController::class, 'getDataDTListInvoice']);
        Route::get('/{id}/edit', [PenjualanReturController::class, 'edit'])->name('edit');
        Route::delete('/destroy/{id}', [PenjualanReturController::class, 'destroy'])->name('destroy');
        Route::resource('/', PenjualanReturController::class)->except(['edit','destroy']);
    });

    Route::prefix('penjualan-tempo')->name('penjualan-tempo.')->group(function () {
        Route::match(['get', 'post'],'/ajaxGetResumePenjualanTempo', [PenjualanTempoController::class, 'ajaxGetResumePenjualanTempo']);
        Route::match(['get', 'post'],'/getDataDTPenjualanTempo', [PenjualanTempoController::class, 'getDataDTPenjualanTempo']);
        Route::resource('/', PenjualanTempoController::class)->except(['edit','destroy']);
    });

    // Route::middleware(['allow.asset.access'])->group(function () {
    //     Route::get('assets/css/{filename}', function ($filename) {
    //         // return response()->file(public_path('assets/css/' . $filename), [
    //         //     'Content-Type' => 'text/css'
    //         // ]);
    //         dd($filename);
    //     })->where('filename', '.*\.css');

    //     Route::get('assets/js/{filename}', function ($filename) {
    //         return response()->file(public_path('assets/js/' . $filename), [
    //             'Content-Type' => 'text/javascript'
    //         ]);
    //     })->where('filename', '.*\.js');
    // });

    Route::delete('/filepicker/ajaxDeleteFile', [FilePickerController::class,'ajaxDeleteFile']);
    Route::post('/filepicker/ajaxUpdateFile', [FilePickerController::class,'ajaxUpdateFile']);
    Route::post('/filepicker/ajaxUploadFile', [FilePickerController::class,'ajaxUploadFile']);
    Route::resource('/filepicker', FilePickerController::class);

    //gudang
    Route::post('/list-barang/SwitchDefault', [GudangController::class, 'switchDefault']);
    Route::resource('/list-gudang', GudangController::class);


    Route::get('/transfer-barang/ajaxGetBarangByBarcode', [TransferBarangController::class, 'ajaxGetBarangByBarcode']);
    Route::get('/transfer-barang/getDataDTListBarang', [TransferBarangController::class, 'getDataDTListBarang']);
    Route::post('/transfer-barang/getDataBarang', [TransferBarangController::class, 'getDataBarang']);
    Route::resource('/transfer-barang', TransferBarangController::class);

    //cetak barcoder barang
    Route::prefix('barcode-cetak')->name('barcode-cetak.')->group(function () {
        Route::get('/ajaxGetBarangByBarcode', [BarcodeCetakController::class, 'ajaxGetBarangByBarcode']);
        Route::post('/getDataBarang', [BarcodeCetakController::class, 'getDataBarang']);
        Route::get('/getDataDTListBarang', [BarcodeCetakController::class, 'getDataDTListBarang']);
        Route::get('/', [BarcodeCetakController::class, 'index'])->name('index');
    });


    //barang
    Route::prefix('barang')->name('barang.')->group(function () {
        Route::get('/GenerateBarcodeNumber', [BarangController::class, 'ajaxGenerateBarcodeNumber']);
        Route::get('/exportExcel', [BarangController::class, 'generateExcel'])->name('exportExcel');
        Route::get('/{id}/edit', [BarangController::class, 'edit'])->name('edit');
        Route::match(['PUT', 'PATCH'], '/update/{barang}', [BarangController::class, 'update'])->name('update');
        Route::delete('/destroy/{barang}', [BarangController::class, 'destroy'])->name('destroy');
        Route::resource('/', BarangController::class)->except(['edit', 'update', 'destroy']);
    });
    //user
    Route::post('/check-username', [UserController::class, 'checkUsername']);
    Route::post('/check-email', [UserController::class, 'checkEmail']);
    Route::resource('/user', UserController::class);

    //setting group
    Route::resource('/jenis-harga', JenisHargaController::class);
    Route::resource('/invoice', SettingInvoiceController::class)->only('index', 'store');
    Route::resource('/pajak', SettingPajakController::class)->only('index', 'store');

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
