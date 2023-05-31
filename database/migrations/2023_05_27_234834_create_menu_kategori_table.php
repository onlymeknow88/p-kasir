<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuKategoriTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori')->nullable();
            $table->string('deskripsi')->nullable();
            $table->enum('aktif',['Y','N'])->default('N');
            $table->enum('show_title',['Y','N'])->default('N');
            $table->integer('urut')->default('0');
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
        Schema::dropIfExists('menu_kategori');
    }
}
