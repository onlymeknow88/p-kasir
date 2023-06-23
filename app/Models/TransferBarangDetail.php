<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferBarangDetail extends Model
{
    use HasFactory;

    protected $table = 'transfer_barang_detail';

    protected $fillable = [
        'transfer_barang_id',
        'barang_id',
        'harga_satuan',
        'qty_transfer',
        'harga_total_transfer',
        'diskon_jenis_transfer',
        'diskon_nilai_transfer',
        'diskon_transfer',
        'harga_neto_transfer',
    ];

    public function transferBarang()
    {
        return $this->belongsTo(TransferBarang::class, 'transfer_barang_id');
    }
}
