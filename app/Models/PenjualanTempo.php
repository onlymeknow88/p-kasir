<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;

class PenjualanTempo extends Model
{
    use HasFactory;

    public function getResumePenjualanTempoByDate($start_date, $end_date, $setting_piutang)
    {
        return DB::table('penjualan')
            ->selectRaw('SUM(total_qty) AS total_qty, SUM(neto) AS total_neto, SUM(total_bayar) AS total_bayar, SUM(neto) - SUM(total_bayar) AS total_piutang')
            ->where('jenis_bayar', 'tempo')
            ->where('status', 'kurang_bayar')
            ->where('tgl_invoice', '>=', $start_date)
            ->where('tgl_invoice', '<=', $end_date)
            ->where(function ($query) use ($setting_piutang) {
                $jatuh_tempo = Request::get('jatuh_tempo');
                if (!empty($jatuh_tempo)) {
                    if ($jatuh_tempo == 'akan_jatuh_tempo') {
                        $query->whereRaw('tgl_penjualan < DATEDIFF(NOW(), tgl_penjualan) > ' . ($setting_piutang['piutang_periode'] - $setting_piutang['notifikasi_periode']) . ' AND DATEDIFF(NOW(), tgl_penjualan) <= ' . $setting_piutang['piutang_periode']);
                    } else if ($jatuh_tempo == 'lewat_jatuh_tempo') {
                        $query->whereRaw('tgl_penjualan < DATE_SUB(NOW(), INTERVAL ' . $setting_piutang['piutang_periode'] . ' DAY)');
                    }
                }
            })
            ->first();
    }
}
