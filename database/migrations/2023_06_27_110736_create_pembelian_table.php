<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembelianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice');
            $table->date('tgl_invoice');
            $table->date('tgl_jatuh_tempo')->nullable();
            $table->enum('terima_barang', ['Y', 'N']);
            $table->date('tgl_terima_barang')->nullable();
            $table->unsignedBigInteger('user_id_terima')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedSmallInteger('gudang_id');
            $table->unsignedBigInteger('sub_total');
            $table->unsignedBigInteger('diskon');
            $table->unsignedBigInteger('total');
            $table->unsignedBigInteger('total_bayar');
            $table->unsignedBigInteger('kurang_bayar');
            $table->enum('status', ['Lunas', 'Belum Lunas'])->default('Belum Lunas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pembelian');
    }
}
