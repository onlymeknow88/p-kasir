<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisHarga extends Model
{
    use HasFactory;

    protected $table = 'jenis_harga';

    protected $fillable = ['nama_jenis_harga','deskripsi','default_harga'];

    public function getHargaJualByIdBarang($id)
    {
        $result = $this->select('*')
            ->selectSub(function ($query) use ($id) {
                $query->select('harga')
                    ->from('barang_harga')
                    ->whereColumn('jenis_harga_id', 'jenis_harga.id')
                    ->where('barang_id', $id)
                    ->where('jenis', 'harga_jual')
                    ->orderByDesc('tgl_input')
                    ->limit(1);
            }, 'harga')
            ->get();

        return $result;
    }
}


