<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PembelianRetur extends Model
{
    use HasFactory;

    protected $table = 'pembelian_retur';

    protected $fillable = ['id_pembelian', 'no_squence', 'no_nota_retur', 'tgl_nota_retur', 'total_qty_retur', 'sub_total_retur', 'total_diskon_item_retur', 'diskon_jenis', 'diskon_nilai', 'penyesuaian', 'neto_retur', 'user_id_input', 'tgl_input', 'user_id_update', 'tgl_update'];

    public function getPembelianReturDetail($id_pembelian_retur)
    {
        // Data
        $result['data'] = $this->select('pembelian.*', 'pembelian_retur.*')
            ->where('pembelian_retur.id', $id_pembelian_retur)
            ->leftJoin('pembelian', 'pembelian_retur.id_pembelian', 'pembelian.id')->first()->toArray();

        if (!$result['data']) {
            return false;
        }

        // Produk
        $result['detail'] = PembelianReturDetail::select('*')
        ->leftJoin('pembelian_detail', 'pembelian_retur_detail.id_pembelian_detail', '=', 'pembelian_detail.id')
        ->leftJoin('barang', 'pembelian_detail.barang_id', '=', 'barang.id')
        ->leftJoin('unit', 'barang.unit_id', '=', 'unit.id')
        ->where('pembelian_retur_detail.id_pembelian_retur', $id_pembelian_retur)
        ->get()
        ->toArray();

        if($result['data']['supplier_id'])
        {
            $result['supplier'] = Supplier::where('id',$result['data']['supplier_id'])->first()->toArray();
        }

        return $result;
    }

    public function getBarangByIdTransferBarang($id)
    {
        $result = PembelianReturDetail::select('*')
            ->leftJoin('pembelian_detail', 'pembelian_detail.id', '=', 'pembelian_retur_detail.id_pembelian_detail')
            ->leftJoin('barang', 'barang.id', '=', 'pembelian_detail.barang_id')
            ->leftJoin('unit', 'unit.id', '=', 'barang.unit_id')
            ->where('id_pembelian_retur', $id)
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
