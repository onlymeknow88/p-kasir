<?php

namespace App\Models;

use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransferBarang extends Model
{
    use HasFactory;

    protected $table = 'transfer_barang';

    protected $fillable = [
        'gudang_asal_id',
        'gudang_tujuan_id',
        'keterangan',
        'no_telp',
        'no_squence',
        'no_nota_transfer',
        'tgl_nota_transfer',
        'jenis_harga_transfer_id',
        'sub_total_transfer',
        'diskon_jenis_transfer',
        'diskon_nilai_transfer',
        'penyesuaian_transfer',
        'total_qty_transfer',
        'total_diskon_item_transfer',
        'neto_transfer',
        'user_id_input',
        'tgl_input',
        'tgl_update',
        'user_id_update'
    ];


    public function getBarangByIdTransferBarang($id)
    {
        $result = DB::table('transfer_barang_detail')->select('transfer_barang_detail.*', 'barang.*', 'unit.satuan')
            ->leftJoin('barang', 'barang.id', '=', 'transfer_barang_detail.barang_id')
            ->leftJoin('unit', 'unit.id', '=', 'barang.unit_id')
            ->where('transfer_barang_id', $id)
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

    public function getListStokByIdBarang($id_barang)
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

    public function getListHargaBarang($id_barang)
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

    public function getBarangByBarcode($code,$gudang_id)
    {
        $data = Barang::select('barang.*', 'unit.nama_satuan', 'detail.stok')
        ->where('barcode', trim($code))
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

        return $data;
    }
}
