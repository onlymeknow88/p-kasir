<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarangHargaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barang_harga', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_id')->nullable();
            $table->unsignedBigInteger('jenis_harga_id')->nullable();
            $table->integer('harga')->nullable();
            $table->enum('jenis',['harga_jual','harga_pokok'])->nullable();
            $table->unsignedBigInteger('user_id_input')->nullable();
            $table->timestamp('tgl_input')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('tgl_update')->nullable();
            $table->unsignedBigInteger('user_id_update')->nullable();
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
        Schema::dropIfExists('barang_harga');
    }
}
