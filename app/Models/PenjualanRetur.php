<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanRetur extends Model
{
    use HasFactory;
    protected $table = 'penjualan_retur';
    protected $fillable = [
        'id_penjualan',
        'id_penjualan',
        'no_nota_retur',
        'tgl_nota_retur',
        'total_qty_retur',
        'sub_total_retur',
        'total_diskon_item_retur',
        'diskon_jenis_retur',
        'diskon_nilai_retur',
        'penyesuaian_retur',
        'neto_retur',
        'user_id_input',
        'tgl_input',
        'user_id_update',
        'tgl_update',
    ];

    public function getBarangByIdTransferBarang($id)
    {
        $result = PenjualanReturDetail::select('*')
            ->leftJoin('penjualan_detail', 'penjualan_detail.id', '=', 'penjualan_retur_detail.id_penjualan_detail')
            ->leftJoin('barang', 'barang.id', '=', 'penjualan_detail.barang_id')
            // ->leftJoin('unit', 'unit.id', '=', 'barang.unit_id')
            ->where('id_penjualan_retur', $id)
            ->get();

        // $data = [];
        // foreach ($result as $val) {
        //     $data[$val->id] = $val;
        // }

        // $id_barang = [];
        // foreach ($data as $val) {
        //     $id_barang[] = $val->id;
        // }

        // $list_stok = $this->getListStokByIdBarang($id_barang);
        // $list_harga = $this->getListHargaBarang($id_barang);

        // foreach ($data as &$val) {
        //     $val->list_stok = $list_stok[$val->id];
        //     $val->list_harga = $list_harga[$val->id];
        // }

        return $result;
    }
}
