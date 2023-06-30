<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelian';

    protected $fillable = ['no_invoice', 'tgl_invoice', 'tgl_jatuh_tempo', 'terima_barang', 'tgl_terima_barang', 'user_id_terima', 'supplier_id', 'gudang_id', 'sub_total', 'diskon', 'total', 'total_bayar', 'kurang_bayar', 'status'];

    public function getDataBarangList($idGudang)
    {
        $subquery = DB::table('barang_adjusment_stok')
            ->select('barang_id', 'gudang_id', DB::raw('SUM(adjusment_stok) AS stok'))
            ->where('gudang_id', $idGudang)
            ->groupBy('barang_id', 'gudang_id')
            ->unionAll(
                DB::table('transfer_barang_detail')
                    ->select('barang_id', 'gudang_asal_id', DB::raw('CAST(qty_transfer AS SIGNED) * -1 AS saldo_stok'))
                    ->leftJoin('transfer_barang', 'transfer_barang.id', '=', 'transfer_barang_detail.transfer_barang_id')
                    ->where('gudang_asal_id', $idGudang)
            )
            ->unionAll(
                DB::table('transfer_barang_detail')
                    ->select('barang_id', 'gudang_tujuan_id', 'qty_transfer AS saldo_stok')
                    ->leftJoin('transfer_barang', 'transfer_barang.id', '=', 'transfer_barang_detail.transfer_barang_id')
                    ->where('gudang_tujuan_id', $idGudang)
            );

        $query = DB::table('barang')
            ->select('barang.*', 'detail.stok', 'unit.satuan')
            ->join('unit', 'unit.id', '=', 'barang.unit_id')
            ->selectSub(function ($query) {
                $query->select('harga')
                    ->from('barang_harga')
                    ->whereColumn('barang_harga.barang_id', 'barang.id')
                    ->where('jenis', 'harga_jual')
                    ->orderByDesc('tgl_input')
                    ->limit(1);
            }, 'harga_jual')
            ->selectSub(function ($query) {
                $query->select('harga')
                    ->from('barang_harga')
                    ->whereColumn('barang_harga.barang_id', 'barang.id')
                    ->where('jenis', 'harga_pokok')
                    ->orderByDesc('tgl_input')
                    ->limit(1);
            }, 'harga_pokok')
            ->joinSub($subquery, 'detail', function ($join) {
                $join->on('barang.id', '=', 'detail.barang_id');
            })
            ->orderByDesc('tgl_input');

        return $query;
    }
}
