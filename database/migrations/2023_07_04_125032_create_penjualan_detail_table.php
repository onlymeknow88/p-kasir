<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenjualanDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_penjualan')->nullable();
            $table->foreign('id_penjualan')->references('id')->on('penjualan')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('barang_id');
            $table->string('satuan', 50);
            $table->unsignedInteger('harga_pokok');
            $table->unsignedInteger('harga_satuan');
            $table->unsignedTinyInteger('qty');
            $table->unsignedInteger('harga_total');
            $table->enum('diskon_jenis', ['%', 'rp']);
            $table->unsignedInteger('diskon_nilai');
            $table->unsignedInteger('diskon');
            $table->unsignedInteger('harga_neto');
            $table->unsignedInteger('harga_pokok_total');
            $table->unsignedInteger('untung_rugi');
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
        Schema::dropIfExists('penjualan_detail');
    }
}
