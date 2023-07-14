<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{

    public function index()
    {
        $data = View::shared('data');
        $settingPiutang = $data['setting_piutang'];

        return view('dashboard',compact('settingPiutang'));
    }
}
