<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferBarangDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_barang_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('transfer_barang_id')->nullable();
            $table->unsignedInteger('barang_id');
            $table->integer('harga_satuan');
            $table->unsignedTinyInteger('qty_transfer');
            $table->unsignedInteger('harga_total_transfer');
            $table->enum('diskon_jenis_transfer', ['%', 'rp']);
            $table->unsignedInteger('diskon_nilai_transfer');
            $table->unsignedInteger('diskon_transfer');
            $table->unsignedInteger('harga_neto_transfer');
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
        Schema::dropIfExists('transfer_barang_detail');
    }
}
