<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenjualaReturDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penjualan_retur_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_penjualan_retur');
            $table->unsignedBigInteger('id_penjualan_detail');
            $table->unsignedTinyInteger('qty_retur');
            $table->unsignedInteger('harga_total_retur');
            $table->enum('diskon_jenis_retur', ['%', 'rp']);
            $table->unsignedInteger('diskon_nilai_retur');
            $table->unsignedInteger('diskon_retur');
            $table->unsignedInteger('harga_neto_retur');

            $table->foreign('id_penjualan_retur')->references('id')->on('penjualan_retur')->onDelete('cascade');
            $table->foreign('id_penjualan_detail')->references('id')->on('penjualan_detail')->onDelete('cascade');
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
        Schema::dropIfExists('penjualan_retur_detail');
    }
}
