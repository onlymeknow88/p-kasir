<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenjualanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedSmallInteger('gudang_id')->nullable();
            $table->mediumInteger('no_squence')->unsigned()->nullable()->default(0);
            $table->string('no_invoice')->nullable();
            $table->date('tgl_invoice')->nullable();
            $table->unsignedSmallInteger('jenis_harga_id')->nullable();
            $table->dateTime('tgl_penjualan')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedInteger('total_qty')->nullable();
            $table->unsignedInteger('total_diskon_item')->nullable();
            $table->enum('diskon_jenis', ['%', 'rp']);
            $table->unsignedInteger('diskon_nilai');
            $table->unsignedInteger('diskon');
            $table->unsignedInteger('total_diskon');
            $table->unsignedInteger('sub_total');
            $table->integer('penyesuaian');
            $table->string('pajak_display_text')->nullable();
            $table->tinyInteger('pajak_persen')->default(0);
            $table->unsignedInteger('pajak_nilai')->default(0);
            $table->unsignedInteger('neto');
            $table->unsignedInteger('total_bayar');
            $table->unsignedInteger('kembali');
            $table->integer('kurang_bayar')->comment('Nilai negatif menunjukkan lebih bayar');
            $table->enum('jenis_bayar', ['tunai', 'tempo'])->nullable();
            $table->enum('status', ['lunas', 'kurang_bayar', 'lebih_bayar']);
            $table->unsignedInteger('harga_pokok');
            $table->integer('untung_rugi');
            $table->unsignedInteger('user_id_input');
            $table->dateTime('tgl_input')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('penjualan');
    }
}
