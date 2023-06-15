<?php

namespace App\Http\Controllers;

use App\Models\SettingApp;
use Illuminate\Http\Request;

class SettingPajakController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //setting invoice
        $setting_pajak = [];
        $setting = SettingApp::where('type', 'pajak')->get()->toArray();

        foreach ($setting as $val) {
            $setting_pajak[$val['param']] = $val['value'];
        }
        return view('page.setting-pajak.form',compact('setting_pajak'));
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
        $data_db[] = ['type' => 'pajak', 'param' => 'display_text', 'value' => $request->display_text];
        $data_db[] = ['type' => 'pajak', 'param' => 'tarif', 'value' => $request->tarif];
        $data_db[] = ['type' => 'pajak', 'param' => 'status', 'value' => $request->status];

        SettingApp::where('type', 'pajak')->delete();
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
