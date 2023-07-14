<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Gudang;
use App\Models\Penjualan;
use App\Models\SettingApp;
use Illuminate\Http\Request;
use App\Models\PenjualanRetur;
use App\Models\PenjualanDetail;
use App\Helpers\ResponseFormatter;
use App\Models\PenjualanReturDetail;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class PenjualanReturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            // Select columns from PembelianRetur table and join with Pembelian and Supplier tables
            $query = PenjualanRetur::select('penjualan_retur.*', 'penjualan.tgl_invoice', 'penjualan.no_invoice', 'customer.nama_customer', 'customer.email')
                ->leftJoin('penjualan', 'Penjualan_retur.id_penjualan', 'penjualan.id')
                ->leftJoin('customer', 'penjualan.customer_id', 'customer.id');
            // dd($query->get());
            // Generate DataTables response
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama_customer', function ($item) {
                    return $item->nama_customer;
                })
                ->addColumn('tgl_invoice', function ($item) {
                    $exp = explode(' ', $item->tgl_invoice);
                    return '<div class="text-end">' . ResponseFormatter::format_tanggal($exp[0]) . '</div>';
                })
                ->addColumn('tgl_nota_retur', function ($item) {
                    $exp = explode(' ', $item->tgl_nota_retur);
                    return '<div class="text-end">' . ResponseFormatter::format_tanggal($exp[0]) . '</div>';
                })
                ->addColumn('neto_retur', function ($item) {
                    return '<div class="text-end">' . ResponseFormatter::format_number($item->neto_retur) . '</div>';
                })
                ->addColumn('ignore_action', function ($item) {
                    return '
                    <div class="d-flex justify-content-start">
                        <a href="' . route('penjualan-retur.edit', $item->id) . '" class="btn btn-icon color-yellow mr-6" title="Edit" >
                            <i class="far fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-icon color-red mr-6 px-2" title="Delete" onclick="deleteData(`' . route('penjualan-retur.destroy', $item->id) . '`)">
                            <i class="far fa-trash-alt text-white"></i>
                        </button>
                    </div>
                    ';
                })
                ->rawColumns(['ignore_action'])
                ->escapeColumns([])
                ->make(true);
        }
        return view('page.penjualan-retur.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $penjualan_retur = new PenjualanRetur;
        $penjualan_retur->tgl_nota_retur = date('Y-m-d');
        $barang = null;
        $gudang = Gudang::pluck('nama_gudang', 'id');
        return view('page.penjualan-retur.form', compact('penjualan_retur', 'barang', 'gudang'));
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

        $rules = [
            'no_nota_retur' => 'required',
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

        $sub_total = 0;
        $total_diskon_item = 0;
        $total_qty = 0;
        $data_db_barang = [];

        foreach ($request->id_penjualan_detail as $key => $id_penjualan_detail) {
            $result = PenjualanDetail::find($id_penjualan_detail);

            $harga_satuan = $result->harga_satuan;
            $qty = str_replace(['.'], '', $request->qty_barang_retur[$key]);
            $harga_barang_retur = $harga_satuan * $qty;

            // Calculate diskon_nilai and diskon_jenis
            $diskon_nilai = str_replace(['.'], '', $request->diskon_barang[$key]);
            $diskon_jenis = $request->diskon_barang_jenis[$key];
            $diskon_harga = 0;

            $is_numeric_diskon_nilai = is_numeric($diskon_nilai);
            $is_numeric_harga_barang_retur = is_numeric($harga_barang_retur);

            if ($is_numeric_diskon_nilai && $is_numeric_harga_barang_retur) {
                if ($diskon_nilai) {
                    $diskon_harga = $diskon_nilai;
                    if ($diskon_jenis == '%') {
                        $diskon_harga = round($harga_barang_retur * $diskon_nilai / 100);
                    }
                    $harga_barang_retur = $harga_barang_retur - $diskon_harga;
                    $total_diskon_item += $diskon_harga;
                }
            }

            $total_qty += $qty;
            $data_db_barang[$key] = [
                'id_penjualan_detail' => $id_penjualan_detail,
                'qty_retur' => $request->qty_barang_retur[$key],
                'harga_total_retur' => $harga_satuan * $qty,
                'diskon_jenis_retur' => $diskon_jenis,
                'diskon_nilai_retur' => $diskon_nilai,
                'diskon_retur' => $diskon_harga,
                'harga_neto_retur' => $harga_barang_retur
            ];
            // Update sub_total
            $sub_total += $harga_barang_retur;
        }

        $data_db = [
            'id_penjualan' => $request->id_penjualan,
            'no_nota_retur' => $request->no_nota_retur,
            'sub_total_retur' => $sub_total,
            'total_diskon_item_retur' => $total_diskon_item,
            'total_qty_retur' => $total_qty,
            'tgl_nota_retur' => Carbon::createFromFormat('d-m-Y', request('tgl_nota_retur'))->format('Y-m-d')
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
        $data_db['diskon_jenis_retur'] = $diskon_total_jenis;
        $data_db['diskon_nilai_retur'] = $diskon_total_nilai != null ? $diskon_total_nilai : 0;
        // Calculate penyesuaian_transfer
        $data_db['penyesuaian_retur'] = str_replace('.', '', $request->penyesuaian_nilai);
        // Calculate neto and update data_db
        $neto_retur = $sub_total + $data_db['penyesuaian_retur'];
        if ($neto_retur < 0) {
            $neto_retur = 0;
        }
        $data_db['neto_retur'] = $neto_retur;

        // Update or create a new TransferBarang record
        if ($request->id) {
            $data_db['user_id_update'] = Auth::user()->id;
            $data_db['tgl_update'] = now();
            $penjualan_retur = PenjualanRetur::findOrFail($request->id);
            $penjualan_retur->update($data_db);
            $id = $request->id;
        } else {
            $data_db['user_id_input'] = Auth::user()->id;
            $id = PenjualanRetur::insertGetId($data_db);
        }


        // Update data_db_barang with id_pembelian_retur
        foreach ($data_db_barang as &$val) {
            $val['id_penjualan_retur'] = $id;
        }
        PenjualanReturDetail::where('id_penjualan_retur', $request->id)->delete();
        PenjualanReturDetail::insert($data_db_barang);
        // Return success response
        return ResponseFormatter::success([
            'data' => $data_db
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
        $penjualan_retur = PenjualanRetur::select('penjualan_retur.*', 'penjualan.no_invoice', 'customer.nama_customer')
            ->leftJoin('penjualan', 'penjualan_retur.id_penjualan', 'penjualan.id')
            ->leftJoin('customer', 'penjualan.customer_id', 'customer.id')
            ->find($id);
        // $penjualan_retur->tgl_nota_retur = date('Y-m-d');
        if (!$penjualan_retur->nama_customer) {
            $penjualan_retur->nama_customer = 'Tamu';
        }
        // dd($penjualan_retur);
        $barang = $penjualan_retur->getBarangByIdTransferBarang($id)->toArray();
        $gudang = Gudang::pluck('nama_gudang', 'id');
        return view('page.penjualan-retur.form', compact('penjualan_retur', 'barang', 'gudang'));
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
        $penjualan_retur = PenjualanRetur::find($id);
        PenjualanReturDetail::where('id_penjualan_retur',$penjualan_retur)->delete();
        $penjualan_retur->delete();

        return ResponseFormatter::success([
            'data' => null
        ], 'Deleted');
    }

    public function getDataDTListInvoice(Request $request)
    {
        return view('page.penjualan-retur.penjualan-retur-list-invoice');
    }

    public function getDataInvoice(Request $request)
    {
        $query = Penjualan::select('penjualan.*', 'customer.nama_customer', 'customer.alamat_customer')
            ->leftJoin('customer', 'penjualan.customer_id', 'customer.id');
        $data = $query->get();
        // dd($data);
        $id_penjualan = [];
        foreach ($data as $val) {
            $id_penjualan[] = $val->id;
        }
        // dd($id_penjualan);
        if ($id_penjualan) {
            $query = PenjualanDetail::select('barang.*', 'penjualan_detail.*')
                ->leftJoin('barang', 'penjualan_detail.barang_id', 'barang.id')
                ->where('id_penjualan', join(',', $id_penjualan));
            $result = $query->get();
            $penjualan_detail = [];
            if ($result) {
                foreach ($result as $val) {
                    $penjualan_detail[$val->id_penjualan][] = $val;
                }

                foreach ($data as &$val) {
                    $val['detail'] = $penjualan_detail[$val->id];
                }
            }
        }
        // dd($data);
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('no_invoice', function ($item) {
                return '<span class="penjualan-detail">' . $item['no_invoice'] . '</span><span style="display:none" class="penjualan">' . json_encode($item) . '</span>';
            })
            ->addColumn('ignore_pilih', function ($item) {
                return  ResponseFormatter::btn_label(['label' => 'Pilih', 'attr' => ['data-id-penjualan' => $item['id'], 'class' => 'btn btn-success pilih-invoice btn-xs']]);
            })
            ->rawColumns(['ignore_pilih'])
            ->escapeColumns([])
            ->make(true);
    }
}
