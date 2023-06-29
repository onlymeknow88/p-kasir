<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = ['kode_barang', 'nama_barang', 'deskripsi', 'unit_id', 'berat', 'kategori_id', 'barcode', 'tgl_input', 'user_id_input', 'tgl_edit', 'user_id_edit'];


    public function getStok($id)
    {
        $result = DB::table(function ($query) use ($id) {
            $query->select('barang_id', 'gudang_id', 'adjusment_stok AS saldo_stok', DB::raw("'adjusment' AS jenis"))
                ->from('barang_adjusment_stok')
                ->where('barang_id', $id);
        }, 'tabel')
            ->leftJoin('gudang', 'tabel.gudang_id', '=', 'gudang.id')
            ->groupBy('tabel.barang_id', 'tabel.gudang_id', 'tabel.saldo_stok')
            ->select('*', DB::raw('SUM(tabel.saldo_stok) AS total_stok'))
            ->get()->toArray();


        return $result;
    }

    public function getDataBarang($id)
    {
        $barang = Barang::where('id', trim($id))->first();
        // $images = [];
        if ($barang) {
            $images = BarangImage::where('id_barang', $barang->id)
                ->leftJoin('file_picker', 'barang_image.file_picker_id', '=', 'file_picker.id')
                ->orderBy('urut')
                ->get();
            // ->toArray();
            // dd($images);
            $barang['images'] = $images;
        }

        return $barang;
    }
}
