<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kategori = Kategori::orderby('urut')->get();
        $kategoris = new Kategori;
        $list_kategori = $kategoris->getHTML($kategori);
        return view('page.barang-kategori.index', compact('list_kategori'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!$request->id) {
            $kategori = new Kategori();
        } else {
            $kategori = Kategori::find($request->id);
        }

        return view('page.barang-kategori.form', compact('kategori'));
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
                'nama_kategori' => ['required', 'string', 'max:255'],
                'deskripsi' => ['required', 'string', 'max:255'],
                // 'aktif' => ['required'],
                // 'parent_id' => ['required'],
                'use_icon' => ['required'],
            ],
            [
                'nama_kategori.required' => 'Silahkan isi nama kategori',
                'deskripsi.required' => 'Silahkan isi deskripsi',
                // 'aktif.required' => 'Silahkan pilih',
                // 'parent_id.required' => 'Silahkan pilih',
                'use_icon.required' => 'Silahkan pilih',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $id = $request->input('id');

        $kategori = Kategori::updateOrCreate(
            [
                'id' => $id
            ],
            [
                'nama_kategori' => $request->input('nama_kategori'),
                'deskripsi' => $request->input('deskripsi'),
                'aktif' => $request->input('aktif') == '' ? 'N' : 'Y',
                'parent_id' => $request->input('parent_id') ?: NULL,
                'icon' => $request->input('use_icon') == 1 ? $request->input('icon_class') : NULL,
            ]
        );

        return ResponseFormatter::success([
            'data' => $kategori
        ], 'Kategori Success');
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
        $kategori = Kategori::findorfail($id);
        $kategori->delete();
        return ResponseFormatter::success([
            'data' => null
        ], 'Kategori Deleted');
    }

    public function ajaxUpdateKategoriUrut(Request $request)
    {
        $kategori = $request->data;

        $json = json_decode(trim($kategori), true);

        $result = $this->updateKategoriItemsUrut($json, null);

        return ResponseFormatter::success(['data', null], 'Kategiru Update Urutan Success');
    }

    private function updateKategoriItemsUrut($menuItems, $parentID)
    {

        foreach ($menuItems as $index => $menuItem) {
            $menu = Kategori::where('id', $menuItem['id'])->first();
            $menu->urut = $index + 1;
            $menu->parent_id = $parentID;
            $menu->save();

            if (isset($menuItem['children']) && count($menuItem['children']) > 0) {
                $this->updateKategoriItemsUrut($menuItem['children'], $menu['id']);
            }
        }
    }
}
