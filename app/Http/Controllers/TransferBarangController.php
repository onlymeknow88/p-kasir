<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\BarangStok;
use App\Models\JenisHarga;
use App\Models\SettingApp;
use Illuminate\Http\Request;
use App\Models\TransferBarang;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Models\TransferBarangDetail;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class TransferBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = DB::table('transfer_barang')
                ->select('transfer_barang.*', 'gudang_asal.nama_gudang AS nama_gudang_asal', 'gudang_tujuan.nama_gudang AS nama_gudang_tujuan')
                ->join('gudang AS gudang_asal', 'gudang_asal.id', '=', 'transfer_barang.gudang_asal_id')
                ->join('gudang AS gudang_tujuan', 'gudang_tujuan.id', '=', 'transfer_barang.gudang_tujuan_id');


            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function ($item) {
                    return '
                    <div class="d-flex justify-content-start">
                        <a href="' . route('transfer-barang.edit', $item->id) . '" class="btn btn-icon color-yellow mr-6 px-2" title="Edit" >
                            <i class="far fa-edit"></i>
                            <span class="form-text-12 fw-bold">Edit</span>
                        </a>
                        <button type="button" class="btn btn-icon color-red mr-6 px-2" title="Delete" onclick="deleteData(`' . route('transfer-barang.destroy', $item->id) . '`)">
                            <i class="far fa-trash-alt text-white"></i>
                            <span class="text-white form-text-12 fw-bold">Hapus</span>
                        </button>
                    </div>
                    ';
                })
                ->rawColumns(['aksi'])
                ->escapeColumns([])
                ->make(true);
        }
        return view('page.transfer-gudang.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Check if the transfer of goods request is new or existing
        $trfBarang = new TransferBarang();
        // Get all necessary data in one query
        $gudang = Gudang::pluck('nama_gudang', 'id');
        $jenis_harga = JenisHarga::pluck('nama_jenis_harga', 'id');
        $jenis_harga_selected = JenisHarga::where('default_harga', 'Y')->value('id');
        $barang = $trfBarang->getBarangByIdTransferBarang($request->id);
        // Return the view with all necessary data
        return view('page.transfer-gudang.form', compact('trfBarang', 'gudang', 'jenis_harga', 'jenis_harga_selected', 'barang'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'gudang_asal_id' => 'required|integer|exists:gudang,id',
            'gudang_tujuan_id' => 'required|integer|exists:gudang,id',
            'keterangan' => 'nullable|string',
            'jenis_harga_id' => 'required|integer|exists:jenis_harga,id',
            'tgl_nota_transfer' => 'required|date_format:d-m-Y',
            'barang_id.*' => 'required|integer|exists:barang,id',
            'harga_satuan.*' => 'required|string',
            'qty_barang.*' => 'required|string',
            'diskon_barang.*' => 'nullable|string',
            'diskon_barang_jenis.*' => 'nullable|string|in:%,Rp',
            'diskon_total_jenis' => 'nullable|string|in:%,Rp',
            'diskon_total_nilai' => 'nullable|string',
            'penyesuaian_operator' => 'nullable|string|in:-,+',
            'penyesuaian_nilai' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Initialize variables
        $sub_total = 0;
        $total_diskon_item = 0;
        $total_qty = 0;
        $data_db_barang = [];
        // Fetch all required Barang records before the loop
        $barangs = Barang::whereIn('id', $request->barang_id)->get()->keyBy('id');
        // Loop through each barang_id in the request
        foreach ($request->barang_id as $key => $barang_id) {
            // Get the corresponding Barang record
            $barang = $barangs->get($barang_id);
            // Calculate harga_satuan, qty, and harga_barang
            $harga_satuan = str_replace(['.'], '', $request->harga_satuan[$key]);
            $qty = str_replace(['.'], '', $request->qty_barang[$key]);
            $harga_barang = $harga_satuan * $qty;
            // Calculate diskon_nilai and diskon_jenis
            $diskon_nilai = str_replace(['.'], '', $request->diskon_barang[$key]);
            $diskon_jenis = $request->diskon_barang_jenis[$key];
            $diskon_harga = 0;
            // Calculate diskon_harga and update harga_barang
            $is_numeric_diskon_nilai = is_numeric($diskon_nilai);
            $is_numeric_harga_barang = is_numeric($harga_barang);
            if ($is_numeric_diskon_nilai && $is_numeric_harga_barang) {
                if ($diskon_nilai) {
                    $diskon_harga = $diskon_nilai;
                    if ($diskon_jenis == '%') {
                        $diskon_harga = round($harga_barang * $diskon_nilai / 100);
                    }
                    $harga_barang = $harga_barang - $diskon_harga;
                    $total_diskon_item += $diskon_harga;
                }
            }
            // Update total_qty and data_db_barang
            $total_qty += $qty;
            $data_db_barang[$key] = [
                'barang_id' => $barang_id,
                'qty_transfer' => $request->qty_barang[$key],
                'harga_satuan' => $harga_satuan,
                'harga_total_transfer' => $harga_satuan * $qty,
                'diskon_jenis_transfer' => $diskon_jenis,
                'diskon_nilai_transfer' => $diskon_nilai,
                'diskon_transfer' => $diskon_harga,
                'harga_neto_transfer' => $harga_barang
            ];
            // Update sub_total
            $sub_total += $harga_barang;
        }
        // Prepare data_db for storing in the database
        $data_db = [
            'gudang_asal_id' => $request->gudang_asal_id,
            'gudang_tujuan_id' => $request->gudang_tujuan_id,
            'keterangan' => $request->keterangan,
            'jenis_harga_transfer_id' => $request->jenis_harga_id,
            'sub_total_transfer' => $sub_total,
            'total_diskon_item_transfer' => $total_diskon_item,
            'total_qty_transfer' => $total_qty,
            'tgl_nota_transfer' => Carbon::createFromFormat('d-m-Y', request('tgl_nota_transfer'))->format('Y-m-d')
        ];
        // Calculate diskon_total_nilai and update sub_total
        $diskon_total_jenis = $request->diskon_total_jenis;
        $diskon_total_nilai = str_replace(['.'], '', $request->diskon_total_nilai);
        if (is_numeric($diskon_total_nilai)) {
            if ($diskon_total_jenis == '%') {
                $sub_total = $sub_total - round($sub_total * $diskon_total_nilai / 100);
            } else {
                $sub_total = $sub_total - $diskon_total_nilai;
            }
        }
        // Update data_db with diskon_jenis_transfer and diskon_nilai_transfer
        $data_db['diskon_jenis_transfer'] = $diskon_total_jenis;
        $data_db['diskon_nilai_transfer'] = $diskon_total_nilai != null ? $diskon_total_nilai : 0;
        // Calculate penyesuaian_transfer
        $operator = '';
        if ($request->penyesuaian_operator == '-') {
            $operator = '-';
        }
        $penyesuaian_nilai = str_replace('.', '', $request->penyesuaian_nilai);
        $data_db['penyesuaian_transfer'] = $operator . (is_numeric($penyesuaian_nilai) ? $penyesuaian_nilai : 0);
        // Calculate neto and update data_db
        $neto = $sub_total + $data_db['penyesuaian_transfer'];
        if ($neto < 0) {
            $neto = 0;
        }
        $data_db['neto_transfer'] = $neto;
        // Check if the request has an id, if not, create a new record
        if (!$request->id) {
            $lockTables = ['transfer_barang WRITE', 'setting WRITE', 'sessions WRITE'];
            DB::transaction(function () use ($lockTables, &$data_db) {
                DB::statement('LOCK TABLES ' . implode(', ', $lockTables));
                $setting = SettingApp::where('type', 'nota_transfer')->get()->keyBy('param');
                $no_nota_transfer_pattern = $setting['no_nota_transfer']['value'];
                $jml_digit = $setting['jml_digit']['value'];
                $maxNoSquence = TransferBarang::where('tgl_nota_transfer', 'LIKE', date('Y') . '%')->max('no_squence');
                $no_squence = $maxNoSquence ? $maxNoSquence + 1 : 1;
                $no_nota_transfer = str_pad($no_squence, $jml_digit, '0', STR_PAD_LEFT);
                $no_nota_transfer = str_replace('{{ nomor }}', $no_nota_transfer, $no_nota_transfer_pattern);
                $no_nota_transfer = str_replace('{{ tahun }}', date('Y'), $no_nota_transfer);
                $data_db['no_nota_transfer'] = $no_nota_transfer;
                $data_db['no_squence'] = $no_squence;
                $transferBarang = TransferBarang::create($data_db);
                DB::statement('UNLOCK TABLES');
            });
        } else {
            $id_transfer_barang = $request->id;
            $data_db['user_id_update'] = Auth::user()->id;
            $data_db['tgl_update'] = now();
            $transferBarang = TransferBarang::find($id_transfer_barang);
            $transferBarang->update($data_db);
        }

        // Update or create a new TransferBarang record
        if ($request->id) {
            $data_db['user_id_update'] = Auth::user()->id;
            $data_db['tgl_update'] = now();
            $transferBarang = TransferBarang::findOrFail($request->id);
            $transferBarang->update($data_db);
            $transfer_barang_id = $request->id;
        } else {
            $data_db['user_id_input'] = Auth::user()->id;
            $id = TransferBarang::latest()->first()->id;
            $transferBarang = TransferBarang::findOrFail($id);
            $transferBarang->update($data_db);
            $transfer_barang_id = $transferBarang->id;
        }
        // Update data_db_barang with transfer_barang_id
        foreach ($data_db_barang as &$val) {
            $val['transfer_barang_id'] = $transfer_barang_id;
        }

        // Delete and insert TransferBarangDetail records
        TransferBarangDetail::where('transfer_barang_id', $request->id)->delete();
        TransferBarangDetail::insert($data_db_barang);
        // Return success response
        return ResponseFormatter::success([
            'data' => $transferBarang
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
        // Get the transfer item by ID
        $trfBarang = TransferBarang::find($id);
        // Get all warehouses and create an array with their IDs and names
        // $warehouses = Gudang::all();
        $gudang = Gudang::pluck('nama_gudang', 'id');
        // dd($warehouses);
        // Create an array of all available price types and set the default price type
        $jenis_harga_items = JenisHarga::select('id', 'nama_jenis_harga', 'default_harga')->get();
        $jenis_harga = $jenis_harga_items->pluck('nama_jenis_harga', 'id')->toArray();
        $jenis_harga_selected = $jenis_harga_items->where('default_harga', 'Y')->pluck('id')->first();
        // Get the item being transferred and set the selected price type
        $barang = $trfBarang->getBarangByIdTransferBarang($id);
        $jenis_harga_selected = $trfBarang->jenis_harga_transfer_id;
        // Return the view with all necessary data
        return view('page.transfer-gudang.form', compact('trfBarang', 'gudang', 'jenis_harga', 'jenis_harga_selected', 'barang'));
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
        DB::transaction(function () use ($id) {
            DB::table('transfer_barang_detail')->where('transfer_barang_id', $id)->delete();
            DB::table('transfer_barang')->where('id', $id)->delete();
        });
        return ResponseFormatter::success([
            'data' => null
        ], 'Deleted');
    }

    public function getDataDTListBarang(Request $request)
    {
        return view('page.transfer-gudang.transfer-barang-list-barang');
    }

    public function getDataBarang(Request $request)
    {
        $idGudang = $request->input('gudang_id');
        $idJenisHarga = $request->input('jenis_harga_id');

        $query =  Barang::select('barang.*', 'detail.stok', 'unit.satuan')
            ->selectSub(function ($query) use ($idJenisHarga) {
                $query->select('harga')
                    ->from('barang_harga')
                    ->where('barang_harga.jenis_harga_id', $idJenisHarga)
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
            ->leftJoinSub(function ($query) use ($idGudang) {
                $query->select('barang_id', DB::raw('SUM(saldo_stok) AS stok'))
                    ->from(function ($subquery) use ($idGudang) {
                        $subquery->select('barang_id', 'gudang_id', 'adjusment_stok AS saldo_stok', DB::raw('"adjusment" AS jenis'))
                            ->from('barang_adjusment_stok')
                            ->where('gudang_id', $idGudang);
                    }, 'tabel')
                    ->groupBy('barang_id');
            }, 'detail', function ($join) {
                $join->on('barang.id', '=', 'detail.barang_id');
            });

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
