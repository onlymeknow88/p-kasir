<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembelianReturDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembelian_retur_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pembelian_detail');
            $table->unsignedBigInteger('id_pembelian_retur');
            $table->unsignedTinyInteger('qty_retur');
            $table->unsignedInteger('harga_total_retur');
            $table->enum('diskon_jenis_retur', ['%', 'rp']);
            $table->unsignedInteger('diskon_nilai_retur');
            $table->unsignedInteger('diskon_retur');
            $table->unsignedInteger('harga_neto_retur');
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
        Schema::dropIfExists('pembelian_retur_detail');
    }
}
