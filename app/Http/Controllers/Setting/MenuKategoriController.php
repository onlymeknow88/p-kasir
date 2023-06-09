<?php

namespace App\Http\Controllers\Setting;

use App\Models\MenuKategori;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MenuKategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        if (!$request->id) {
            $menuKategori = new MenuKategori();
        } else {
            $menuKategori = MenuKategori::find($request->id);
        }
        return view('page.aplikasi.menu.form_kategori', compact('menuKategori'));
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
                'aktif' => ['required'],
                'show_title' => ['required'],
            ],
            [
                'nama_kategori.required' => 'Silahkan isi nama kategori',
                'deskripsi.required' => 'Silahkan isi deskripsi',
                'aktif.required' => 'Silahkan isi pilih',
                'show_title.required' => 'Silahkan pilih',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $menuKategori = MenuKategori::latest()->first();
        $data = $request->except('_method', '_token', 'urut');
        $data['urut'] = $menuKategori->urut + 1;
        $id = $request->id;


        if ($id) {
            $kategori = MenuKategori::find($id);
        } else {

            $kategori = MenuKategori::create($data);
        }


        return ResponseFormatter::success([
            'data' => $kategori
        ], 'Menu Kategori Success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $menuKategori = MenuKategori::find($id);
        return response()->json(['data' => $menuKategori]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
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
        $validator = Validator::make(
            $request->all(),
            [
                'nama_kategori' => ['required', 'string', 'max:255'],
                'deskripsi' => ['required', 'string', 'max:255'],
                'aktif' => ['required'],
                'show_title' => ['required'],
            ],
            [
                'nama_kategori.required' => 'Silahkan isi nama kategori',
                'deskripsi.required' => 'Silahkan isi nama kategori',
                'aktif.required' => 'Silahkan isi nama kategori',
                'show_title.required' => 'Silahkan isi nama kategori',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = $request->all();
        $kategori = MenuKategori::find($id);
        $kategori->update($data);

        return ResponseFormatter::success([
            'data' => $kategori
        ], 'Menu Kategori Success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menu = MenuKategori::findorfail($id);
        $menu->delete();
        return ResponseFormatter::success([
            'data' => null
        ], 'Menu Kategori Deleted');
    }

    public function ajaxUpdateKategoriUrut(Request $request)
    {
        $updated = $this->updateKategoriUrut(json_decode($request->id, true));

    }

    private function updateKategoriUrut($list_kategori)
    {
        $urut = 1;
		foreach ($list_kategori as $id_kategori) {
			$menuKategori = MenuKategori::find($id_kategori);
            $menuKategori->update(['urut' => $urut]);
			$urut++;
		}
    }
}
