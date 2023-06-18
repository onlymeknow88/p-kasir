<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {

            $query = DB::table('barang')
                ->leftJoin('unit', 'barang.id', '=', 'unit.id')
                ->leftJoin(DB::raw('(SELECT barang_id, SUM(saldo_stok) AS stok
                                FROM (SELECT barang_id, gudang_id, adjusment_stok AS saldo_stok, "adjusment" AS jenis
                                      FROM barang_adjusment_stok) AS tabel
                                GROUP BY barang_id) AS tabel_stok'), 'barang.id', '=', 'tabel_stok.barang_id')
                ->select('barang.*', 'tabel_stok.stok');


            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('stok', function ($item) {
                    return $item->stok;
                })
                ->addColumn('aksi', function ($item) {
                    return '
                    <div class="d-flex justify-content-start">
                        <a href="' . route('barang.edit', $item->id) . '" class="btn btn-icon color-yellow mr-6 px-2" title="Edit" >
                            <i class="far fa-edit"></i>
                            <span class="form-text-12 fw-bold">Edit</span>
                        </a>
                        <button type="button" class="btn btn-icon color-red mr-6 px-2" title="Delete" onclick="deleteData(`' . route('barang.destroy', $item->id) . '`)">
                            <i class="far fa-trash-alt text-white"></i>
                            <span class="text-white form-text-12 fw-bold">Hapus</span>
                        </button>
                    </div>
                    ';
                })
                ->rawColumns(['aksi', 'stok'])
                ->escapeColumns([])
                ->make(true);
        }
        return view('page.barang.index');
        // $id = 1;

        // $query = Barang::select('barang.*')
        //     ->leftJoin('unit', 'barang.unit_id', '=', 'unit.id')
        //     ->leftJoin(DB::raw('(
        //             SELECT barang_id, SUM(saldo_stok) AS stok FROM (
        //                 SELECT barang_id, gudang_id, adjusment_stok AS saldo_stok, "adjusment" AS jenis
        //                 FROM barang_adjusment_stok
        //             ) AS tabel
        //             GROUP BY barang_id
        //         ) AS tabel_stok'), 'barang.id', '=', 'tabel_stok.barang_id')->get();
        // return response()->json(['data' => $query]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $barang = new Barang();

        //list barang kategori
        $kategoris = new Kategori;
        $result = $kategoris->getKategori();
        $getResult = $kategoris->kategori_list($result);
        $list_kategori = $kategoris->buildKategoriList($getResult);

        //list satuan unit
        $satuan_unit = Unit::all();
        return view('page.barang.form', compact('barang','list_kategori','satuan_unit'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function ajaxGenerateBarcodeNumber($repeat = false)
    {
        $add = $repeat ? rand(1, 60) : 0;
		$number = time() + $add;
		$digit = '899' . substr($number, 0, 9);
		$split = str_split($digit);

		$sum_genab = 0;
		$sum_ganjil = 0;
		foreach ($split as $key => &$val) {
			if ( ($key + 1) % 2 ) {
				$sum_ganjil = $sum_ganjil + $val;
			} else {
				$sum_genab = $sum_genab + $val;
			}
		}

		$sum_genab = $sum_genab * 3;
		$sum = $sum_genab + $sum_ganjil;

		$sisa = $sum % 10;
		if ($sisa == 0) {
			$check_digit = 0;
		} else {
			$check_digit = 10 - $sisa;
		}

		$barcode_number = $digit . $check_digit;
        return $barcode_number;
        // return response()->json(['data' => $barcode_number]);
    }



}
