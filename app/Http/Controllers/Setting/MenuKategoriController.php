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
        $kategori = MenuKategori::create($data);

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
        //
    }
}
