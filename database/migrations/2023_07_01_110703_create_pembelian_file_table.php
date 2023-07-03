<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembelianFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembelian_file', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pembelian');
            $table->unsignedBigInteger('file_picker_id');
            $table->unsignedTinyInteger('urut');
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
        Schema::dropIfExists('pembelian_file');
    }
}
