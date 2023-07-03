<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianReturDetail extends Model
{
    use HasFactory;

    protected $table = 'pembelian_retur_detail';

    protected $fillable = ['id_pembelian_retur', 'qty_retur', 'harga_total_retur', 'diskon_jenis_retur', 'diskon_nilai_retur', 'diskon_retur', 'harga_neto_retur'];

    
}
