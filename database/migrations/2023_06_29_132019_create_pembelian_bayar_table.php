<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembelianBayarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembelian_bayar', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_pembelian')->nullable();
            $table->date('tgl_bayar')->nullable();
            $table->unsignedInteger('jml_bayar')->nullable();
            $table->unsignedInteger('user_id_bayar')->nullable()->comment('Approval');
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
        Schema::dropIfExists('pembelian_bayar');
    }
}
