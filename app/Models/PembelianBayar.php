<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianBayar extends Model
{
    use HasFactory;

    protected $table = 'pembelian_bayar';

    protected $fillable = ['id_pembelian', 'tgl_bayar', 'jml_bayar', 'user_id_bayar'];
}
