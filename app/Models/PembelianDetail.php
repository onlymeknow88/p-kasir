<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    use HasFactory;

    protected $table = 'pembelian_detail';

    protected $fillable = ['id_pembelian', 'barang_id', 'qty', 'expired_date','harga_satuan','harga_neto','keterangan'];
}
