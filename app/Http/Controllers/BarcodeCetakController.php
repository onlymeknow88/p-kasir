<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Yajra\DataTables\Facades\DataTables;

class BarcodeCetakController extends Controller
{
    public function index()
    {
        return view('page.barcode.form');
    }

    public function ajaxGetBarangByBarcode(Request $request)
    {
        $data = Barang::where('barcode', $request->code)->first();
        if ($data) {
            $result = ['status' => 'ok', 'data' => $data];
        } else {
            $result = ['status' => 'error', 'message' => 'Data tidak ditemukan'];
        }
        return  response()->json($result);
    }

    public function getDataDTListBarang(Request $request)
    {
        return view('page.barcode.barcode-list-barang');
    }

    public function getDataBarang(Request $request)
    {
        $query =  Barang::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_barang', function ($item) {
                return '<span class="nama-barang">' . $item->nama_barang . '</span><span style="display:none" class="detail-barang">' . json_encode($item) . '</span>';;
            })
            ->addColumn('ignore_pilih', function ($item) {
                return  ResponseFormatter::btn_label(['label' => 'Pilih', 'attr' => ['data-id-barang' => $item->id,'class'=>'btn btn-success pilih-barang btn-xs']]);
            })
            // ->rawColumns([])
            ->escapeColumns([])
            ->make(true);
    }
}
