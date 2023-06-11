<?php

namespace App\Http\Controllers\Setting;

use App\Models\SettingApp;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class SettingAppController extends Controller
{
    public function index()
    {
        $data = [];
        $setting_app = SettingApp::where('type', 'app')->get()->toArray();

        foreach ($setting_app as $val) {
            $data[$val['param']] = $val['value'];
        }

        return view('page.aplikasi.setting-app.form', compact('data'));
    }

    public function store(Request $request)
    {
        $arr = SettingApp::where('type', 'app')->get()->toArray();

        foreach ($arr as $key => $val) {
            $curr_db[$val['param']] = $val['value'];
        }

        $path = 'assets/img/';

        // Logo Login
        $logo_login = $curr_db['logo_login'];

        //old logo_login
        if ($curr_db['logo_login']) {
            if ($request->hasFile('logo_login')) {
                $old_login_path = base_path('assets/img/', $logo_login);
                if (file_exists($old_login_path)) {
                    unlink($old_login_path);
                }
                $logo_login = ResponseFormatter::upload_file($path,$request->logo_login);
            }
        }

        //Logo Aplikasi
        $logo_app = $curr_db['logo_app'];

         //old logo_login
         if ($curr_db['logo_app']) {
            if ($request->hasFile('logo_app')) {
                $old_app_path = base_path('assets/img/', $logo_app);
                if (file_exists($old_app_path)) {
                    unlink($old_app_path);
                }

                $logo_app = ResponseFormatter::upload_file($path,$request->logo_app);
            }
        }

        //favicon
        $favicon = $curr_db['favicon'];

         //old logo_login
         if ($curr_db['favicon']) {
            if ($request->hasFile('favicon')) {
                $favicon = base_path('assets/img/', $logo_app);
                if (file_exists($favicon)) {
                    unlink($favicon);
                }

                $favicon = ResponseFormatter::upload_file($path,$request->favicon);
            }
        }

        $data_db =[];
        if($logo_login) {
            $data_db[] = ['type' => 'app', 'param' => 'logo_login', 'value' => $logo_login];
            $data_db[] = ['type' => 'app', 'param' => 'logo_app', 'value' => $logo_app];
            $data_db[] = ['type' => 'app', 'param' => 'footer_login', 'value' => htmlentities($request->footer_login)];
			$data_db[] = ['type' => 'app', 'param' => 'footer_app', 'value' => htmlentities($request->footer_app)];
			$data_db[] = ['type' => 'app', 'param' => 'background_logo', 'value' => $request->background_logo];
			$data_db[] = ['type' => 'app', 'param' => 'judul_web', 'value' => $request->judul_web];
			// $data_db[] = ['type' => 'app', 'param' => 'deskripsi_web', 'value' => $_POST['deskripsi_web']];
			$data_db[] = ['type' => 'app', 'param' => 'favicon', 'value' => $favicon];
			// $data_db[] = ['type' => 'app', 'param' => 'logo_register', 'value' => $logo_register];
        }

        SettingApp::where('type', 'app')->delete();
        SettingApp::insert($data_db);
    }
}
