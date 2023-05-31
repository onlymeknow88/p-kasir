<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->string('nama_menu')->nullable();
            $table->string('class',50)->nullable();
            $table->string('url',50)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('menu')->onDelete('cascade');
            $table->unsignedBigInteger('menu_kategori_id')->nullable();
            $table->foreign('menu_kategori_id')->references('id')->on('menu_kategori')->onDelete('cascade');
            $table->enum('aktif',['Y','N'])->default('N');
            $table->integer('new')->default('0');
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
        Schema::dropIfExists('menu');
    }
}
