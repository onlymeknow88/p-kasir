<?php

namespace App\Http\Controllers;

use App\Models\SettingApp;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;

class SettingInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //setting invoice
        $setting_invoice = [];
        $setting_inv = SettingApp::where('type', 'invoice')->get()->toArray();

        foreach ($setting_inv as $val) {
            $setting_invoice[$val['param']] = $val['value'];
        }

        //setting nota retur
        $setting_nota_retur = [];
        $setting_nota = SettingApp::where('type', 'nota_retur')->get()->toArray();

        foreach ($setting_nota as $val) {
            $setting_nota_retur[$val['param']] = $val['value'];
        }

        //setting nota retur
        $setting_nota_transfer = [];
        $setting_transfer = SettingApp::where('type', 'nota_transfer')->get()->toArray();

        foreach ($setting_transfer as $val) {
            $setting_nota_transfer[$val['param']] = $val['value'];
        }

        return view('page.setting-invoice.form', compact('setting_invoice', 'setting_nota_retur', 'setting_nota_transfer'));
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

        $result = [];

        $data_db[] = ['type' => 'invoice', 'param' => 'footer_text', 'value' => htmlentities($request->footer_text)];
        $data_db[] = ['type' => 'invoice', 'param' => 'no_invoice', 'value' => htmlentities($request->no_invoice)];
        $data_db[] = ['type' => 'invoice', 'param' => 'jml_digit', 'value' => $request->jml_digit_invoice];
        $data_db[] = ['type' => 'nota_retur', 'param' => 'jml_digit', 'value' => $request->jml_digit_nota_retur];
        $data_db[] = ['type' => 'nota_retur', 'param' => 'no_nota_retur', 'value' => $request->no_nota_retur];
        $data_db[] = ['type' => 'nota_transfer', 'param' => 'jml_digit', 'value' => $request->jml_digit_nota_transfer];
        $data_db[] = ['type' => 'nota_transfer', 'param' => 'no_nota_transfer', 'value' => $request->no_nota_transfer];

        $arr_invoice = SettingApp::where('type', 'invoice')->where('param', 'logo')->first()->toArray();

        $logo_invoice_lama = $arr_invoice['value'];

        $path = 'assets/img/';

        //old logo
        if ($request->hasFIle('logo')) {

            if ($logo_invoice_lama) {
                $old_logo_path = base_path('assets/img/', $logo_invoice_lama);
                if (file_exists($old_logo_path)) {
                    unlink($old_logo_path);
                }
            }
            $logo = ResponseFormatter::upload_file($path, $request->logo);
            $data_db[] = ['type' => 'invoice', 'param' => 'logo', 'value' => $logo];
        } else {
            $data_db[] = ['type' => 'invoice', 'param' => 'logo', 'value' => $logo_invoice_lama];
        }
        SettingApp::where('type', 'invoice')->delete();
        SettingApp::where('type', 'nota_retur')->delete();
        SettingApp::where('type', 'nota_transfer')->delete();
        SettingApp::insert($data_db);

        return redirect()->back();
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
