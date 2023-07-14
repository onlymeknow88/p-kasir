<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $fillable = ['customer_id', 'gudang_id', 'no_invoice', 'tgl_invoice', 'jenis_harga_id', 'tgl_penjualan', 'total_qty', 'total_diskon_item', 'diskon_jenis', 'diskon_nilai', 'diskon', 'total_diskon', 'sub_total', 'penyesuaian', 'pajak_display_text', 'pajak_persen', 'pajak_nilai', 'neto', 'total_bayar', 'kembali', 'kurang_bayar', 'jenis_bayar', 'status', 'harga_pokok','untung_rugi','user_id_input','tgl_input','user_id_update','tgl_update'];

    public function getBarangByIdTransferBarang($id)
    {
        $result = DB::table('penjualan_detail')->select('penjualan_detail.*', 'barang.*', 'unit.satuan')
            ->leftJoin('barang', 'barang.id', '=', 'penjualan_detail.barang_id')
            ->leftJoin('unit', 'unit.id', '=', 'barang.unit_id')
            ->where('id_penjualan', $id)
            ->get();

        $data = [];
        foreach ($result as $val) {
            $data[$val->id] = $val;
        }

        $id_barang = [];
        foreach ($data as $val) {
            $id_barang[] = $val->id;
        }

        $list_stok = $this->getListStokByIdBarang($id_barang);
        $list_harga = $this->getListHargaBarang($id_barang);

        foreach ($data as &$val) {
            $val->list_stok = $list_stok[$val->id];
            $val->list_harga = $list_harga[$val->id];
        }

        return $data;
    }

    private function getListStokByIdBarang($id_barang)
    {
        $listStok = BarangStok::select('barang_id', 'gudang_id', DB::raw('SUM(adjusment_stok) AS stok'))
            ->leftJoin('gudang', 'gudang.id', '=', 'barang_adjusment_stok.gudang_id')
            ->whereIn('barang_id', $id_barang)
            ->groupBy('barang_id', 'gudang_id')
            ->get();

        $list_stok = [];
        foreach ($listStok as $stok) {
            $list_stok[$stok->barang_id][$stok->gudang_id] = $stok->stok;
        }

        // dd($list_stok);
        return $list_stok;
    }

    private function getListHargaBarang($id_barang)
    {
        $jenisHarga = DB::table('Jenis_harga')->select('*')->get()->toArray();
        $list_harga = [];

        foreach ($jenisHarga as $jenis) {
            $result_harga = DB::table('barang')->select('id', DB::raw('(SELECT harga
                    FROM barang_harga
                    WHERE jenis_harga_id = ' . $jenis->id . '
                    AND barang_id = barang.id
                    AND jenis = "harga_jual"
                    ORDER BY tgl_input DESC
                    LIMIT 1
                ) AS harga_jual'))
                ->whereIn('id', $id_barang)
                ->get()->toArray();

            foreach ($result_harga as $val_harga) {
                $list_harga[$val_harga->id][$jenis->id] = $val_harga->harga_jual;
            }
        }

        return $list_harga;
    }

    public function getBarangByBarcode($code, $gudang_id, $jenis_harga_id)
    {
        $data = Barang::select('barang.*', 'unit.satuan', 'detail.stok')
            ->where('barcode', trim($code))
            ->selectSub(function ($query) use ($jenis_harga_id) {
                $query->select('harga')
                    ->from('barang_harga')
                    ->where('barang_harga.jenis_harga_id', $jenis_harga_id)
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
            ->leftJoin('unit', 'unit.id', '=', 'barang.unit_id')
            ->leftJoinSub(function ($query) use ($gudang_id) {
                $query->select('barang_id', DB::raw('SUM(saldo_stok) AS stok'))
                    ->from(function ($subquery) use ($gudang_id) {
                        $subquery->select('barang_id', 'gudang_id', 'adjusment_stok AS saldo_stok', DB::raw('"adjusment" AS jenis'))
                            ->from('barang_adjusment_stok')
                            ->where('gudang_id', $gudang_id);
                    }, 'tabel')
                    ->groupBy('barang_id');
            }, 'detail', function ($join) {
                $join->on('barang.id', '=', 'detail.barang_id');
            })
            ->first();

        // dd($data);

        return $data;
    }
}
