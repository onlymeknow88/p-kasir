<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SettingApp extends Model
{
    use HasFactory;

    protected $table = 'setting';

    protected $fillable = ['type', 'param', 'value'];

    public function getSetting($type)
    {
        $settings = $this->where('type', $type)->get();
        return $settings->toArray();
    }

    public function getJmlPiutangLewatJatuhTempo($setting_piutang) {
        return DB::table('penjualan')
            ->selectRaw('COUNT(*) AS jml')
            ->where('status', 'kurang_bayar')
            ->whereRaw('tgl_penjualan < DATE_SUB(NOW(), INTERVAL ' . $setting_piutang['piutang_periode'] . ' DAY)')
            ->first()->jml;
    }
     public function getJmlPiutangAkanJatuhTempo($setting_piutang) {
        return DB::table('penjualan')
            ->selectRaw('COUNT(*) AS jml')
            ->where('status', 'kurang_bayar')
            ->whereRaw('DATEDIFF(NOW(), tgl_penjualan) > ' . ($setting_piutang['piutang_periode'] - $setting_piutang['notifikasi_periode']) . ' AND DATEDIFF(NOW(), tgl_penjualan) <= ' . $setting_piutang['piutang_periode'])
            ->first()->jml;
    }
     public function getPeriodePenjualanPiutang() {
        return DB::table('penjualan')
            ->selectRaw('DATE(MIN(tgl_penjualan)) AS start_date, DATE(MAX(tgl_penjualan)) AS end_date')
            ->first();
    }

}
