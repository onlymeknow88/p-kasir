<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarangAdjusmentStokTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barang_adjusment_stok', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_id')->nullable();
            $table->unsignedBigInteger('gudang_id')->nullable();
            $table->integer('adjusment_stok')->nullable();
            $table->timestamp('tgl_input')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedBigInteger('user_id_input')->nullable();
            $table->dateTime('tgl_update');
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
        Schema::dropIfExists('barang_adjusment_stok');
    }
}
