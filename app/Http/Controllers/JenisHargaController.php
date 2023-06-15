<?php

namespace App\Http\Controllers;

use App\Models\JenisHarga;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class JenisHargaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = JenisHarga::query();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function ($item) {
                    return '
                    <div class="d-flex justify-content-start">
                        <button onclick="showForm(\'edit\','.$item->id.')" class="btn btn-icon color-yellow mr-6 px-2" title="Edit" >
                            <i class="far fa-edit"></i>
                            <span class="form-text-12 fw-bold">Edit</span>
                        </button>
                        <button type="button" class="btn btn-icon color-red mr-6 px-2" title="Delete" onclick="deleteData(`' . route('jenis-harga.destroy', $item->id) . '`)">
                            <i class="far fa-trash-alt text-white"></i>
                            <span class="text-white form-text-12 fw-bold">Hapus</span>
                        </button>
                    </div>
                    ';
                })
                ->rawColumns(['aksi'])
                ->escapeColumns([])
                ->make(true);
        }

        return view('page.jenis-harga.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!$request->id) {
            $jenisharga = new JenisHarga();
        } else {
            $jenisharga = JenisHarga::find($request->id);
        }

        return view('page.jenis-harga.form',compact('jenisharga'));
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
                'nama_jenis_harga' => ['required', 'string', 'max:255'],
                'deskripsi' => ['required', 'string', 'max:255'],
            ],
            [
                'nama_jenis_harga.required' => 'Silahkan isi nama jenis harga',
                'deskripsi.required' => 'Silahkan isi deskripsi',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $id = $request->input('id');

        $unit = JenisHarga::updateOrCreate(
            [
                'id' => $id
            ],
            [
                'nama_jenis_harga' => $request->input('nama_jenis_harga'),
                'deskripsi' => $request->input('deskripsi'),
            ]
        );

        return ResponseFormatter::success([
            'data' => $unit
        ], 'Jenis Harga Success');
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
        $jenisharga = JenisHarga::find($id);
        $jenisharga->delete();
        return ResponseFormatter::success([
            'data' => null
        ], 'Deleted');
    }
}
