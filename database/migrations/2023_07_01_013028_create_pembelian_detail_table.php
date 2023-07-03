<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembelianDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembelian_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pembelian');
            $table->unsignedBigInteger('barang_id');
            $table->unsignedSmallInteger('qty')->nullable();
            $table->date('expired_date')->nullable();
            $table->unsignedInteger('harga_satuan')->nullable();
            $table->unsignedInteger('harga_neto')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_pembelian')->references('id')->on('pembelian')->onDelete('cascade');
            $table->foreign('barang_id')->references('id')->on('barang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pembelian_detail');
    }
}
