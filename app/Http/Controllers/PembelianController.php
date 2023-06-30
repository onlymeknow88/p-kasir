<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\Supplier;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use App\Models\PembelianBayar;
use App\Models\TransferBarang;
use Illuminate\Support\Carbon;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Pembelian::select('pembelian.*', 'supplier.*',)
                ->leftJoin('supplier', 'pembelian.supplier_id', 'supplier.id');


            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('ignore_search_action', function ($item) {
                    return '
                    <div class="d-flex justify-content-start">
                        <a href="' . route('daftar-pembelian.edit', $item->id) . '" class="btn btn-icon color-yellow mr-6 px-2" title="Edit" >
                            <i class="far fa-edit"></i>
                            <span class="form-text-12 fw-bold">Edit</span>
                        </a>
                        <button type="button" class="btn btn-icon color-red mr-6 px-2" title="Delete" onclick="deleteData(`' . route('daftar-pembelian.destroy', $item->id) . '`)">
                            <i class="far fa-trash-alt text-white"></i>
                            <span class="text-white form-text-12 fw-bold">Hapus</span>
                        </button>
                    </div>
                    ';
                })
                ->rawColumns(['ignore_search_action'])
                ->escapeColumns([])
                ->make(true);
        }
        return view('page.pembelian.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pembelian = new Pembelian();
        $pembelian['tanda_terima'] = 'N';
        // dd($pembelian);
        $supplier = Supplier::pluck('nama_supplier', 'id');
        $gudang = Gudang::pluck('nama_gudang', 'id');
        $user = User::pluck('name', 'id');
        return view('page.pembelian.form', compact('pembelian', 'supplier', 'gudang', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation rules
    $rules = [
        'no_invoice' => 'required',
        'supplier_id' => 'required',
        'gudang_id' => 'required',
        'tgl_invoice' => 'required|date_format:d-m-Y',
        'tgl_jatuh_tempo' => 'required|date_format:d-m-Y',
        'sub_total' => 'required|numeric',
        'diskon' => 'required|numeric',
        'total_bayar' => 'required|numeric',
        'kurang_bayar' => 'required|numeric',
        'terima_barang' => 'required',
        'tgl_terima_barang' => 'nullable|date_format:d-m-Y',
        'user_id_terima' => 'nullable',
        'using_pembayaran' => 'nullable',
        'qty.*' => 'required|numeric',
        'harga_satuan.*' => 'required|numeric',
        'harga_neto.*' => 'required|numeric',
        'keterangan.*' => 'nullable',
        'tgl_bayar.*' => 'nullable|date_format:d-m-Y',
        'jml_bayar.*' => 'nullable|numeric',
        'user_id_bayar.*' => 'nullable',
        'file_picker_id.*' => 'nullable',
    ];

    // Validate the request data
    $validator = Validator::make($request->all(), $rules);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422);
    }

        $data_db['no_invoice'] = $request->input('no_invoice');
        $data_db['supplier_id'] = $request->input('supplier_id');
        $data_db['gudang_id'] = $request->input('gudang_id');
        $data_db['tgl_invoice'] = Carbon::createFromFormat('d-m-Y', $request->input('tgl_invoice'))->format('Y-m-d');
        $data_db['tgl_jatuh_tempo'] = Carbon::createFromFormat('d-m-Y', $request->input('tgl_jatuh_tempo'))->format('Y-m-d');
        $data_db['sub_total'] = (int)str_replace('.', '', trim($request->input('sub_total')));
        $data_db['diskon'] = (int)str_replace('.', '', trim($request->input('diskon')));
        $total = max(0, $data_db['sub_total'] - $data_db['diskon']);
        $data_db['total'] = $total;
        $data_db['total_bayar'] = (int)str_replace('.', '', trim($request->input('total_bayar')));
        $data_db['kurang_bayar'] = (int)str_replace('.', '', trim($request->input('kurang_bayar')));
        $data_db['status'] = $data_db['kurang_bayar'] > 0 ? 'Belum Lunas' : 'Lunas';
        $data_db['kurang_bayar'] = max(0, $data_db['total'] - $data_db['kurang_bayar']);
        $data_db['terima_barang'] = $request->input('terima_barang');
        $data_db['tgl_terima_barang'] = '0000-00-00';
        $data_db['user_id_terima'] = null;
        if ($request->input('terima_barang') === 'Y') {
            $data_db['tgl_terima_barang'] = Carbon::createFromFormat('d-m-Y', $request->input('tgl_terima_barang'))->format('Y-m-d');
            $data_db['user_id_terima'] = $request->input('user_id_terima');
        }
        $id_pembelian = '';
        if ($request->input('id')) {
            $query = Pembelian::where('id', $request->input('id'))->update($data_db);
            $id_pembelian = $request->input('id');
        } else {
            $pembelian = Pembelian::create($data_db);
            $id_pembelian = $pembelian->id;

        }

        // dd($request->input('using_pembayaran'));
        // DB::table('pembelian_detail')->where('id_pembelian', $id_pembelian)->delete();
        // $details = [];
        // foreach ($request->input('qty') as $key => $val) {
        //     $detail = [];
        //     $detail['id'] = $id_pembelian;
        //     $detail['barang_id'] = $request->input('barang_id')[$key];
        //     $expired_date = Carbon::createFromFormat('d-m-Y', $request->input('expired_date')[$key]);
        //     $detail['expired_date'] = $expired_date ? $expired_date->format('Y-m-d') : '';
        //     $detail['qty'] = (int)str_replace('.', '', $val);
        //     $detail['harga_satuan'] = (int)str_replace('.', '', $request->input('harga_satuan')[$key]);
        //     $detail['harga_neto'] = (int)str_replace('.', '', $request->input('harga_neto')[$key]);
        //     $detail['keterangan'] = $request->input('keterangan')[$key];
        //     $details[] = $detail;
        // }
        // DB::table('pembelian_detail')->insert($details);

        if ($request->input('using_pembayaran')) {
            PembelianBayar::where('id_pembelian', $id_pembelian)->delete();
             $pembelianBayarData = [];
            foreach ($request->input('tgl_bayar') as $key => $val) {
                $pembelianBayarData[] = [
                    'id_pembelian' => $id_pembelian,
                    'tgl_bayar' => Carbon::createFromFormat('d-m-Y', $val)->format('Y-m-d'),
                    'jml_bayar' => str_replace('.', '', $request->input('jml_bayar')[$key]),
                    'user_id_bayar' => $request->input('user_id_bayar')[$key]
                ];
            }
            // dd($pembelianBayarData);
            PembelianBayar::insert($pembelianBayarData);
        }
        //  if ($request->input('id')) {
        //     DB::table('pembelian_file')->where('id_pembelian', $id_pembelian)->delete();
        // }
        //  $pembelianFileData = [];
        // foreach ($request->input('file_picker_id') as $index => $val) {
        //     if ($val) {
        //         $pembelianFileData[] = [
        //             'file_picker_id' => $val,
        //             'id_pembelian' => $id_pembelian,
        //             'urut' => ($index + 1)
        //         ];
        //     }
        // }
        //  if (!empty($pembelianFileData)) {
        //     DB::table('pembelian_file')->insert($pembelianFileData);
        // }

        return ResponseFormatter::success([
            'data' => $pembelian
        ], 'Success');
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

    public function getDataDTListBarang(Request $request)
    {
        return view('page.pembelian.daftar-pembelian-list-barang');
    }

    public function getDataBarang(Request $request)
    {
        $idGudang = $request->input('gudang_id');

        $pembelian = new Pembelian;

        $query = $pembelian->getDataBarangList($idGudang);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_barang', function ($item) {
                return '<span class="nama-barang">' . $item->nama_barang . '</span><span style="display:none" class="detail-barang">' . json_encode($item) . '</span>';;
            })
            ->addColumn('ignore_stok', function ($item) {
                return $item->stok;
            })
            ->addColumn('ignore_satuan', function ($item) {
                return $item->satuan;
            })
            ->addColumn('ignore_harga_pokok', function ($item) {
                return '<div class="text-end">' . ResponseFormatter::format_number($item->harga_pokok) . '</div>';
            })
            ->addColumn('ignore_harga_jual', function ($item) {
                return '<div class="text-end">' . ResponseFormatter::format_number($item->harga_jual) . '</div>';
            })
            ->addColumn('ignore_pilih', function ($item) {
                $attr_btn = ['data-id-barang' => $item->id, 'class' => 'btn btn-success pilih-barang btn-xs'];
                if ($item->stok == 0) {
                    $attr_btn['disabled'] = 'disabled';
                }
                return  ResponseFormatter::btn_label(['label' => 'Pilih', 'attr' => $attr_btn]);
            })
            ->rawColumns(['ignore_stok', 'ignore_satuan'])
            ->escapeColumns([])
            ->make(true);
    }

    public function ajaxGetBarangByBarcode(Request $request)
    {
        $code = $request->code;
        $idGudang = $request->gudang_id;
        $trfBarang = new TransferBarang;
        $data = $trfBarang->getBarangByBarcode($code, $idGudang);

        if ($data) {
            $result = ['status' => 'ok', 'data' => $data];
        } else {
            $result = ['status' => 'error', 'message' => 'Data tidak ditemukan'];
        }

        return  response()->json($result);
    }
}
