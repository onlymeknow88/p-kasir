<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanBayar extends Model
{
    use HasFactory;

    protected $table = 'penjualan_bayar';
    protected $fillable = ['id_penjualan', 'tgl_bayar', 'jml_bayar'];
}
