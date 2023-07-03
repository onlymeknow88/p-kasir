<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembelianReturTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembelian_retur', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pembelian')->nullable();
            $table->mediumInteger('no_squence')->unsigned()->nullable();
            $table->string('no_nota_retur')->nullable();
            $table->date('tgl_nota_retur')->nullable();
            $table->unsignedInteger('total_qty_retur')->nullable();
            $table->unsignedInteger('sub_total_retur');
            $table->unsignedInteger('total_diskon_item_retur');
            $table->enum('diskon_jenis', ['%', 'rp']);
            $table->unsignedInteger('diskon_nilai');
            $table->integer('penyesuaian');
            $table->integer('neto_retur');
            $table->unsignedInteger('user_id_input');
            $table->datetime('tgl_input');
            $table->unsignedInteger('user_id_update')->nullable();
            $table->datetime('tgl_update')->nullable();
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
        Schema::dropIfExists('pembelian_retur');
    }
}
