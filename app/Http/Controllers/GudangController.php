<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class GudangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Gudang::query();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('default', function ($item) {
                    $checked = $item->default_gudang == 'Y' ? 'checked' : '';
                    return '<div class="form-switch">
                                <input name="aktif" type="checkbox" class="form-check-input switch" data-id-gudang="' . $item->id . '" ' . $checked . '>
                            </div>';
                })
                ->addColumn('aksi', function ($item) {
                    return '
                    <div class="d-flex justify-content-start">
                        <button onclick="showForm(\'edit\',' . $item->id . ')" class="btn btn-icon color-yellow mr-6 px-2" title="Edit" >
                            <i class="far fa-edit"></i>
                            <span class="form-text-12 fw-bold">Edit</span>
                        </button>
                        <button type="button" class="btn btn-icon color-red mr-6 px-2" title="Delete" onclick="deleteData(`' . route('list-gudang.destroy', $item->id) . '`)">
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
        return view('page.gudang.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!$request->id) {
            $gudang = new Gudang();
        } else {
            $gudang = Gudang::find($request->id);
        }
        return view('page.gudang.form', compact('gudang'));
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
                'nama_gudang' => ['required', 'string', 'max:255'],
                'alamat_gudang' => ['required', 'string', 'max:255'],
                'deskripsi' => ['required', 'string', 'max:255'],
                'default_gudang' => ['required'],
            ],
            [
                'nama_gudang.required' => 'Silahkan isi nama nama gudang',
                'alamat_gudang.required' => 'Silahkan isi nama alamat gudang',
                'deskripsi.required' => 'Silahkan isi deskripsi',
                'default_gudang.required' => 'Silahkan isi default',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $id = $request->input('id');

        $gudang = Gudang::updateOrCreate(
            [
                'id' => $id
            ],
            [
                'nama_gudang' => $request->input('nama_gudang'),
                'alamat_gudang' => $request->input('alamat_gudang'),
                'deskripsi' => $request->input('deskripsi'),
                'default_gudang' => $request->input('default_gudang'),
            ]
        );

        return ResponseFormatter::success([
            'data' => $gudang
        ], 'Success');
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

        $gudang = Gudang::find($id);
        $gudang->delete();
        return ResponseFormatter::success([
            'data' => null
        ], 'Deleted');
    }

    public function switchDefault(Request $request)
    {
        // Get the count of the default gudang
        $defaultGudangCount = Gudang::where('default_gudang', 'Y')->count();
        // Check if there is only one default gudang and the requested gudang is not set as default
        if ($defaultGudangCount == 1 && $request->default_gudang == 'N') {
            return response()->json(['status' => 'error', 'message' => 'Setidaknya ada satu gudang yang dipilih menjadi default']);
        }
        // Set the default gudang to 'N' for all gudangs
        Gudang::where('default_gudang', 'Y')->update(['default_gudang' => 'N']);
        // Find the requested gudang
        $requestedGudang = Gudang::find($request->id);
        // If the requested gudang is not found, return an error response
        if (!$requestedGudang) {
            return response()->json(['status' => 'error', 'message' => 'Data gagal disimpan']);
        }
        // Set the requested gudang as the default gudang and save the changes
        $requestedGudang->default_gudang = 'Y';
        $requestedGudang->save();
        // Return a success response
        return response()->json(['status' => 'ok', 'message' => 'Data berhasil disimpan']);
    }
}
