<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use App\Models\PenjualanTempo;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;


class PenjualanTempoController extends Controller
{
    protected $settingPiutang, $model;


    public function __construct()
    {
        $data = View::shared('data');
        $this->settingPiutang = $data['setting_piutang'];

        $this->model = new PenjualanTempo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // dd($this->settingPiutang);
        if (!empty($request->start_date)) {
            list($y, $m, $d) = explode('-', $request->start_date);
            $start_date = $d . '-' . $m . '-' . $y;
        } else {
            $start_date = date('d-n-Y', strtotime('-1 month'));
        }

        if (!empty($request->end_date)) {
            list($y, $m, $d) = explode('-', $request->end_date);
            $end_date = $d . '-' . $m . '-' . $y;
        } else {
            $end_date = date('d-n-Y');
        }

        $exp = explode('-', $start_date);
        $start_date_db = $exp[2] . '-' . substr('0' . $exp[1], -2) . '-' . $exp[0];

        $exp = explode('-', $end_date);
        $end_date_db = $exp[2] . '-' . substr('0' . $exp[1], -2) . '-' . $exp[0];

        $data['total_penjualan'] = $this->model->getResumePenjualanTempoByDate($start_date_db, $end_date_db, $this->settingPiutang);
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['start_date_db'] = $start_date_db;
        $data['end_date_db'] = $end_date_db;

        $jatuh_tempo = "";
        if (!empty($request->jatuh_tempo)) {
            $jatuh_tempo = $request->jatuh_tempo;
        }
        $data['jatuh_tempo'] = $jatuh_tempo;
        $setting_piutang = $this->settingPiutang;

        return view('page.penjualan-tempo.index', compact('data', 'setting_piutang'));
    }

    public function ajaxGetResumePenjualanTempo(Request $request)
    {
        $result = $this->model->getResumePenjualanTempoByDate($request->start_date, $request->end_date, $this->settingPiutang);
        echo json_encode($result);
    }

    public function getDataDTPenjualanTempo(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $query = Penjualan::leftJoin('customer', 'penjualan.customer_id', '=', 'customer.id')
            ->where('jenis_bayar', 'tempo')
            ->where('status', 'kurang_bayar')
            ->whereBetween('tgl_invoice', [$startDate, $endDate])
            ->where(function ($query) use ($request) {
                $jatuh_tempo = $request->jatuh_tempo;
                if (!empty($jatuh_tempo)) {
                    if ($jatuh_tempo == 'akan_jatuh_tempo') {
                        $piutang_periode = $this->settingPiutang['piutang_periode'];
                        $notifikasi_periode = $this->settingPiutang['notifikasi_periode'];
                        $query->whereRaw('tgl_penjualan < DATEDIFF(NOW(), tgl_penjualan) > ' . ($piutang_periode - $notifikasi_periode) . ' AND DATEDIFF(NOW(), tgl_penjualan) <= ' . $piutang_periode);
                    } else if ($jatuh_tempo == 'lewat_jatuh_tempo') {
                        $piutang_periode = $this->settingPiutang['piutang_periode'];
                        $query->whereRaw('tgl_penjualan < DATE_SUB(NOW(), INTERVAL ' . $piutang_periode . ' DAY)');
                    }
                }
            }); // Change the number to the desired page size

            // dd($query->get());
        //  $data = $query->query();
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_customer', function ($item) {
                return $item['nama_customer'] ?: '-';
            })
            ->addColumn('tgl_penjualan', function ($item) {
                $exp = explode(' ', $item->tgl_penjualan);
                return '<div class="text-end">' . ResponseFormatter::format_tanggal($exp[0]) . '</div>';
            })
            ->addColumn('neto', function ($item) {
                return  '<div class="text-end">' . ResponseFormatter::format_number($item->neto) . '</div>';
            })
            ->addColumn('total_bayar', function ($item) {
                return  '<div class="text-end">' . ResponseFormatter::format_number($item->total_bayar) . '</div>';
            })
            ->addColumn('kurang_bayar', function ($item) {
                return  '<div class="text-end">' . ResponseFormatter::format_number($item->kurang_bayar) . '</div>';
            })
            ->rawColumns([])
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
}
