<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang',50)->default(0);
            $table->string('nama_barang')->nullable();
            $table->string('deskripsi')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('berat')->nullable();
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->string('barcode',13)->nullable();
            $table->timestamp('tgl_input')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedBigInteger('user_id_input')->nullable();
            $table->dateTime('tgl_edit')->nullable();
            $table->unsignedBigInteger('user_id_edit')->nullable();
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
        Schema::dropIfExists('barang');
    }
}
