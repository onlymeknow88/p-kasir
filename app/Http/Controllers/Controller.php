<?php

namespace App\Http\Controllers;

use App\Models\SettingApp;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $model;
    protected $setting_piutang = [];

    public function __construct()
    {
        $this->model = new SettingApp;
    }

    public function setNotifikasiPiutang()
    {
        $result = $this->model->getSetting('piutang');
        $setting_piutang = array_column($result, 'value', 'param');
        $data['setting_piutang'] = $setting_piutang;
        if (isset($setting_piutang['notifikasi_show']) && $setting_piutang['notifikasi_show'] == 'Y') {
            $data['setting_piutang']['jml_lewat_jatuh_tempo'] = $this->model->getJmlPiutangLewatJatuhTempo($setting_piutang);
            $data['setting_piutang']['jml_akan_jatuh_tempo'] = $this->model->getJmlPiutangAkanJatuhTempo($setting_piutang);
            $data['setting_piutang']['periode_penjualan_piutang'] = $this->model->getPeriodePenjualanPiutang();
            $data['setting_piutang']['default_jatuh_tempo_option'] = 'lewat_jatuh_tempo';
        }

        // dd($data);

        return $data;
    }
}
