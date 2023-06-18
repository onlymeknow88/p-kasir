<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangStok extends Model
{
    use HasFactory;

    protected $table = 'barang_adjusment_stok';

    protected $fillable = ['barang_id', 'gudang_id', 'adjusment_stok', 'tgl_input', 'user_id_input', 'tgl_update', 'user_id_update'];

    public function barang()
    {
        return $this->belongsTo(Barang::class,'barang_id');
    }
}
