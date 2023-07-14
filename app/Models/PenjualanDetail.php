<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    use HasFactory;

    protected $table = 'penjualan_detail';


    protected $fillable = ['id_penjualan', 'barang_id', 'satuan', 'harga_pokok', 'harga_satuan', 'qty', 'total_qty', 'harga_total', 'diskon_jenis', 'diskon_nilai', 'diskon', 'harga_pokok_total', 'harga_neto', 'untung_rugi'];
}
