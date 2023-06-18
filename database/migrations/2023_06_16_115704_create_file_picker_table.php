<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilePickerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_picker', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('caption');
            $table->string('description');
            $table->string('alt_text');
            $table->string('nama_file');
            $table->string('mime_type');
            $table->unsignedInteger('size');
            $table->timestamp('tgl_upload')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedBigInteger('user_id_upload');
            $table->string('meta_file');
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
        Schema::dropIfExists('file_picker');
    }
}
