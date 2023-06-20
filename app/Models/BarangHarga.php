<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangHarga extends Model
{
    use HasFactory;

    protected $table = 'barang_harga';

    protected $fillable = ['barang_id', 'jenis_harga_id', 'harga', 'jenis', 'user_id_input', 'tgl_input', 'tgl_update', 'user_id_update'];

    public function getLatestHargaPokok($id)
    {
        $result = $this->where('id', $id)
            ->where('jenis', 'harga_pokok')
            ->orderBy('tgl_input', 'desc')
            ->select('harga')
            ->first();

        if ($result) {
            return $result->harga;
        }

        return $result;
    }

   

}
