<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferBarangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_barang', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('gudang_asal_id')->nullable();
            $table->unsignedSmallInteger('gudang_tujuan_id')->nullable();
            $table->text('keterangan')->nullable();
            $table->mediumInteger('no_squence')->unsigned()->default(0);
            $table->string('no_nota_transfer')->nullable();
            $table->date('tgl_nota_transfer')->nullable();
            $table->unsignedSmallInteger('jenis_harga_transfer_id')->nullable();
            $table->unsignedInteger('sub_total_transfer');
            $table->enum('diskon_jenis_transfer', ['%', 'rp']);
            $table->unsignedInteger('diskon_nilai_transfer');
            $table->integer('penyesuaian_transfer');
            $table->unsignedInteger('total_qty_transfer')->nullable();
            $table->unsignedInteger('total_diskon_item_transfer');
            $table->unsignedInteger('neto_transfer');
            $table->unsignedInteger('user_id_input');
            $table->dateTime('tgl_input');
            $table->unsignedInteger('user_id_update')->nullable();
            $table->dateTime('tgl_update')->nullable();
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
        Schema::dropIfExists('transfer_barang');
    }
}
