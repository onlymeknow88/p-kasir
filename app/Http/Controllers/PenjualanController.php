<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\Customer;
use App\Models\Penjualan;
use App\Models\JenisHarga;
use App\Models\SettingApp;
use Illuminate\Http\Request;
use App\Models\PenjualanBayar;
use App\Models\TransferBarang;
use App\Models\PenjualanDetail;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Penjualan::select('penjualan.*', 'customer.nama_customer',)
                ->leftJoin('customer', 'penjualan.customer_id', 'customer.id');


            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama_customer', function ($item) {
                    return $item->nama_customer ?: '-';
                })
                ->addColumn('tgl_penjualan', function ($item) {
                    $exp = explode(' ', $item->tgl_penjualan);
                    return '<div class="text-end">' . ResponseFormatter::format_tanggal($exp[0]) . '</div>';
                })
                ->addColumn('neto', function ($item) {
                    return '<div class="text-end">' . ResponseFormatter::format_number($item->neto) . '</div>';
                })
                ->addColumn('untung_rugi', function ($item) {
                    return '<div class="text-end">' . ResponseFormatter::format_number($item->untung_rugi) . '</div>';
                })
                ->addColumn('kurang_bayar', function ($item) {
                    if ($item->kurang_bayar < 0) {
                        $item->kurang_bayar = 0;
                    }
                    return '<div class="text-end">' . ResponseFormatter::format_number($item->kurang_bayar) . '</div>';
                })
                ->addColumn('status', function ($item) {
                    if ($item->status == 'kurang_bayar') {
                        $item->status = 'kurang';
                    }
                    return ucfirst($item->status);
                })
                ->addColumn('ignore_action', function ($item) {
                    return '
                    <div class="d-flex justify-content-start">
                        <a href="' . route('penjualan-list.edit', $item->id) . '" class="btn btn-icon color-yellow mr-6 px-2" title="Edit" >
                            <i class="far fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-icon color-red mr-6 px-2" title="Delete" onclick="deleteData(`' . route('penjualan-list.destroy', $item->id) . '`)">
                            <i class="far fa-trash-alt text-white"></i>
                        </button>
                    </div>
                    ';
                })
                ->addColumn('ignore_invoice', function ($item) {
                    $attr_btn_email = ['label' => '', 'icon' => 'fas fa-paper-plane', 'attr' => ['data-url' => '/penjualan/invoicePdf?email=Y&id=' . $item->id, 'data-id' => $item->id, 'class' => 'btn btn-icon color-blue text-white btn-xs kirim-email']];
                    if ($item->email) {
                        $attr_btn_email['attr']['data-bs-toggle'] = 'tooltip';
                        $attr_btn_email['attr']['data-bs-title'] = 'Kirim Invoice ke Email';
                    } else {
                        $attr_btn_email['attr']['disabled'] = 'disabled';
                        $attr_btn_email['attr']['class'] = $attr_btn_email['attr']['class'] . ' disabled';
                    }

                    $url_nota = '/penjualan/printNota?id=' . $item->id;
                    return '<div class="btn-action-group">'
                        . ResponseFormatter::btn_link(['url' => $url_nota, 'label' => '', 'icon' => 'fas fa-print', 'attr' => ['data-url' => $url_nota, 'class' => 'btn btn-icon color-softgray-2  px-2 print-nota me-1', 'data-bs-toggle' => 'tooltip', 'data-bs-title' => 'Print Nota']])
                        . ResponseFormatter::btn_link(['url' => '/penjualan/invoicePdf?id=' . $item->id, 'label' => '', 'icon' => 'fas fa-file-pdf', 'attr' => ['data-filename' => 'Invoice-' . $item->no_invoice, 'target' => '_blank', 'class' => 'btn btn-icon color-red text-white px-2 save-pdf me-1', 'data-bs-toggle' => 'tooltip', 'data-bs-title' => 'Download Invoice (PDF)']])
                        . ResponseFormatter::btn_label($attr_btn_email)
                        . '</div>';
                })
                ->rawColumns(['ignore_action', 'ignore_invoice'])
                ->escapeColumns([])
                ->make(true);
        }
        return view('page.penjualan.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $penjualan = new Penjualan();
        $gudang = Gudang::pluck('nama_gudang', 'id');
        $jenis_harga = JenisHarga::pluck('nama_jenis_harga', 'id');
        $jenis_harga_selected = JenisHarga::where('default_harga', 'Y')->value('id');
        $pajak = SettingApp::where('type', 'pajak')->pluck('value', 'param')->all();
        $barang = $penjualan->getBarangByIdTransferBarang($request->id);
        // dd($pajak);
        return view('page.penjualan.form', compact('penjualan', 'gudang', 'jenis_harga', 'jenis_harga_selected', 'pajak'));
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
            'tgl_invoice' => 'required',
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

        // Initialize variables
        $sub_total = 0;
        $total_diskon_item = 0;
        $total_qty = 0;
        $total_untung_rugi = 0;
        $total_harga_pokok = 0;
        $data_db_barang = [];
        // Loop through all barang_id in the request
        foreach ($request->barang_id as $key => $barang_id) {
            // Get barang record from the database
            $barangs = Barang::select('unit.satuan')->where('barang.id', $barang_id)
                ->leftJoin('unit', 'barang.unit_id', 'unit.id')->first();
            // Calculate harga_satuan, qty, and harga_barang
            $harga_satuan = str_replace(['.'], '', $request->harga_satuan[$key]);
            $qty = str_replace(['.'], '', $request->qty[$key]);
            $harga_barang = $harga_satuan * $qty;
            // Calculate diskon_nilai, diskon_jenis, and diskon_harga
            $diskon_nilai = str_replace(['.'], '', $request->diskon_barang_nilai[$key]);
            $diskon_jenis = $request->diskon_barang_jenis[$key];
            $diskon_harga = 0;
            // If diskon_nilai and harga_barang are numeric, calculate new diskon_harga and harga_barang
            if (is_numeric($diskon_nilai) && is_numeric($harga_barang)) {
                if ($diskon_nilai) {
                    $diskon_harga = $diskon_nilai;
                    if ($diskon_jenis == '%') {
                        $diskon_harga = round($harga_barang * $diskon_nilai / 100);
                    }
                    $harga_barang = $harga_barang - $diskon_harga;
                    $total_diskon_item += $diskon_harga;
                }
            }
            // Calculate total_qty, untung_rugi, total_untung_rugi, and total_harga_pokok
            $total_qty += $qty;
            $untung_rugi = $harga_barang - $request->harga_pokok[$key] * $qty;
            $total_untung_rugi += $untung_rugi;
            $total_harga_pokok += $request->harga_pokok[$key] *  $qty;
            // Prepare data_db_barang array
            $data_db_barang[$key] = [
                'barang_id' => $barang_id,
                'qty' => $request->qty[$key],
                'satuan' => $barangs->satuan,
                'harga_pokok' => $request->harga_pokok[$key],
                'harga_satuan' => $harga_satuan,
                'harga_total' => $harga_satuan * $qty,
                'diskon_jenis' => $diskon_jenis,
                'diskon_nilai' => $diskon_nilai,
                'diskon' => $diskon_harga,
                'harga_neto' => $harga_barang,
                'harga_pokok_total' => $request->harga_pokok[$key] * $qty,
                'untung_rugi' => $untung_rugi,
            ];
            // Update sub_total
            $sub_total += $harga_barang;
        }

        $data_db = [
            'gudang_id' => $request->gudang_id,
            'jenis_harga_id' => $request->jenis_harga_id,
            'sub_total' => $sub_total,
            'total_diskon_item' => $total_diskon_item,
            'total_qty' => $total_qty,
            'jenis_harga_id' => $request->jenis_harga_id,
            'harga_pokok' => $total_harga_pokok,
            'untung_rugi' => $total_untung_rugi,
        ];

        if (empty($request->id)) {
            $setting = SettingApp::where('type', 'invoice')
                ->whereIn('param', ['no_invoice', 'jml_digit'])
                ->get()
                ->keyBy('param');
            $pola_no_invoice = $setting['no_invoice']->value;
            $jml_digit = $setting['jml_digit']->value;
            $no_squence = Penjualan::where('tgl_invoice', 'like', date('Y') . '%')
                ->max('no_squence') + 1;
            $no_invoice = str_pad($no_squence, $jml_digit, "0", STR_PAD_LEFT);
            $no_invoice = str_replace('{{ nomor }}', $no_invoice, $pola_no_invoice);
            $no_invoice = str_replace('{{ tahun }}', date('Y'), $no_invoice);
            $data_db['no_invoice'] = $no_invoice;
            $data_db['no_squence'] = $no_squence;
            $data_db['tgl_invoice'] = date('Y-m-d');
            $data_db['tgl_penjualan'] = date('Y-m-d H:i:s');
        } else {
            $exp = explode('-', $request->tgl_invoice);
            $data_db['tgl_invoice'] = $exp[2] . '-' . $exp[1] . '-' . $exp[0];
        }
        // If customer_id is present in the request, assign it to data_db, else assign null
        $data_db['customer_id'] = $request->customer_id ? $request->customer_id : null;
        // Calculate diskon_total_nilai and update sub_total
        $diskon_total_jenis = $request->diskon_total_jenis;
        $diskon_total_nilai = str_replace(['.'], '', $request->diskon_total_nilai);
        $diskon = 0;
        if (is_numeric($diskon_total_nilai)) {
            if ($diskon_total_jenis == '%') {
                $sub_total = $sub_total - round($sub_total * $diskon_total_nilai / 100);
                $diskon = round($sub_total * $diskon_total_nilai / 100);
            } else {
                $sub_total = $sub_total - $diskon_total_nilai;
                $diskon = $diskon_total_nilai;
            }
        }
        // Update data_db with diskon and total_diskon
        $data_db['diskon'] = $diskon;
        $data_db['total_diskon'] = $total_diskon_item + $diskon;
        $data_db['diskon_jenis'] = $diskon_total_jenis;
        $data_db['diskon_nilai'] = $diskon_total_nilai != null ? $diskon_total_nilai : 0;
        // Calculate penyesuaian
        $operator = '';
        if ($request->penyesuaian_operator == '-') {
            $operator = '-';
        }
        $penyesuaian_nilai = str_replace('.', '', $request->penyesuaian_nilai);
        $data_db['penyesuaian'] = $operator . (is_numeric($penyesuaian_nilai) ? $penyesuaian_nilai : 0);
        // Calculate neto and update data_db
        $neto = $sub_total + $data_db['penyesuaian'];
        if ($neto < 0) {
            $neto = 0;
        }
        $data_db['neto'] = $neto;

        $total_bayar = 0;
        foreach ($request->jml_bayar as $key => $val) {
            $total_bayar += (int) str_replace('.', '', $val);
        }
        $data_db['total_bayar'] = $total_bayar;
        $data_db['kurang_bayar'] = $neto - $total_bayar;
        if ($total_bayar >= $neto) {
            $status = 'lunas';
            $data_db['kembali'] = $total_bayar - $neto;
        } else {
            $status = 'kurang_bayar';
            $data_db['kembali'] = 0;
        }
        $data_db['status'] = $status;

        // Pajak
        $data_db['pajak_persen'] = $data_db['pajak_nilai'] = 0;
        $data_db['pajak_display_text'] = null;
        if (!empty($request->pajak_nilai)) {
            $setting = $this->getSetting('pajak');

            foreach ($setting as $val) {
                $pajak_setting[$val['param']] = $val['value'];
            }

            $pajakRate = $request->pajak_nilai;
            $pajak = round($neto * $pajakRate / 100);
            $neto += $pajak;

            $data_db['pajak_display_text'] = $pajak_setting['display_text'];
            $data_db['pajak_persen'] = $pajakRate;
            $data_db['pajak_nilai'] = $pajak;
        }
        // Update or create a new TransferBarang record
        if ($request->id) {
            $data_db['user_id_update'] = Auth::user()->id;
            $data_db['tgl_update'] = now();
            $penjualan = Penjualan::findOrFail($request->id);
            $penjualan->update($data_db);
            PenjualanDetail::where('id_penjualan', $request->id)->delete();
            PenjualanBayar::where('id_penjualan', $request->id)->delete();
            $id = $request->id;
        } else {
            $data_db['user_id_input'] = Auth::user()->id;
            $id = Penjualan::insertGetId($data_db);
        }

        if ($total_bayar) {
            foreach ($request->jml_bayar as $key => $val) {
                $data_db_bayar[$key]['id_penjualan'] = $id;
                $data_db_bayar[$key]['jml_bayar'] = str_replace('.', '', $val);
                $data_db_bayar[$key]['tgl_bayar'] = ResponseFormatter::format_datedb($request->tgl_bayar[$key]);
            }
            PenjualanBayar::insert($data_db_bayar);
        }

        // Update data_db_barang with id_penjualan
        foreach ($data_db_barang as &$val) {
            $val['id_penjualan'] = $id;
        }

        // Delete and insert PenjualanDetail records
        PenjualanDetail::where('id_penjualan', $request->id)->delete();
        PenjualanDetail::insert($data_db_barang);

        return ResponseFormatter::success([
            'data' => $data_db
        ], 'Success');
    }

    private function getSetting($type)
    {
        $settings = SettingApp::where('type', $type)->get();
        return $settings->toArray();
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
        $penjualan = Penjualan::find($id);
        // dd($penjualan);
        $gudang = Gudang::pluck('nama_gudang', 'id');
        $jenis_harga = JenisHarga::pluck('nama_jenis_harga', 'id');
        $jenis_harga_selected = JenisHarga::where('default_harga', 'Y')->value('id');
        $pajak = SettingApp::where('type', 'pajak')->pluck('value', 'param')->all();
        $result = new Penjualan;
        $barang = $result->getBarangByIdTransferBarang($id);
        $pembayaran = PenjualanBayar::where('id_penjualan', $penjualan->id)->get()->toArray();
        // dd($pajak);
        return view('page.penjualan.form', compact('penjualan', 'barang', 'gudang', 'jenis_harga', 'jenis_harga_selected', 'pajak', 'pembayaran'));
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
        $penjualan = Penjualan::find($id);
        $penjualan->delete();

        return ResponseFormatter::success([
            'data' => null
        ], 'Deleted');
    }

    public function getListCustomer(Request $request)
    {
        return view('page.penjualan.penjualan-customer-list');
    }

    public function getDataDTCustomer(Request $request)
    {
        $query = Customer::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('ignore_pilih', function ($item) {
                $attr_btn = ['data-id-customer' => $item->customer_id, 'class' => 'btn btn-success pilih-customer btn-xs'];
                return  ResponseFormatter::btn_label(['label' => 'Pilih', 'attr' => $attr_btn]) . '<span style="display:none">' . $item . '</span>';
            })
            ->rawColumns(['ignore_stok', 'ignore_satuan'])
            ->escapeColumns([])
            ->make(true);
    }

    public function getDataDTListBarang(Request $request)
    {
        return view('page.penjualan.penjualan-barang-list');
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
        $idJenisHarga = $request->jenis_harga_id;
        $penjualan = new Penjualan;
        $data = $penjualan->getBarangByBarcode($code, $idGudang, $idJenisHarga);

        if ($data) {
            $result = ['status' => 'ok', 'data' => $data];
        } else {
            $result = ['status' => 'error', 'message' => 'Data tidak ditemukan'];
        }

        return  response()->json($result);
    }
}
