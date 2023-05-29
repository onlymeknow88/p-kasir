<?php

namespace App\Http\Controllers\Setting;

use App\Models\Menu;
use App\Models\MenuKategori;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menuKategori = MenuKategori::all();
        return view('page.setting.menu.index', compact('menuKategori'));
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
        $validator = Validator::make(
            $request->all(),
            [
                'nama_menu' => ['required', 'string', 'max:255'],
                'url' => ['required', 'string', 'max:255'],
                'aktif' => ['required'],
                'module_id' => ['required'],
                'use_icon' => ['required'],
                'menu_kategori_id' => ['required'],
            ],
            [
                'nama_menu.required' => 'Silahkan isi nama menu',
                'url.required' => 'Silahkan isi url',
                'aktif.required' => 'Silahkan pilih',
                'module_id.required' => 'Silahkan pilih',
                'use_icon.required' => 'Silahkan pilih',
                'menu_kategori_id.required' => 'Silahkan pilih',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $menu = new Menu();

        $menu->nama_menu = $request->input('nama_menu');
        $menu->url = $request->input('url');

        if(empty($request->input('aktif'))){
            $aktif = 'N';
        }else {
            $aktif = 'Y';
        }

        $menu->aktif = $aktif;

        $menu->module_id = $request->input('module_id');
        $menu->class = $request->input('icon_class');

        if($request->input('menu_kategori_id') == '') {
            $menu_kategori_id = NULL;
        } else {
            $menu_kategori_id = $request->input('menu_kategori_id');
        }
        $menu->menu_kategori_id = $menu_kategori_id;
        // $menu->save();

        return ResponseFormatter::success([
            'data' => $menu
        ], 'Menu Success');
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
