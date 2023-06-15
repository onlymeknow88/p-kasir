<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Supplier::query();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function ($item) {
                    return '
                    <div class="d-flex justify-content-start">
                        <a href="' . route('supplier.edit', $item->id) . '" class="btn btn-icon color-yellow mr-6 px-2" title="Edit" >
                            <i class="far fa-edit"></i>
                            <span class="form-text-12 fw-bold">Edit</span>
                        </a>
                        <button type="button" class="btn btn-icon color-red mr-6 px-2" title="Delete" onclick="deleteData(`' . route('supplier.destroy', $item->id) . '`)">
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
        return view('page.supplier.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $supplier = new Supplier();
        return view('page.supplier.form', compact('supplier'));
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
                'nama_supplier' => ['required', 'string', 'max:255'],
                'alamat_supplier' => ['required', 'string', 'max:255'],
                'no_telp' => ['required', 'string', 'max:255'],
            ],
            [
                'nama_supplier.required' => 'Silahkan isi nama supplier',
                'alamat_supplier.required' => 'Silahkan isi alamat supplier',
                'no_telp.required' => 'Silahkan isi no telp',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        $supplier = Supplier::create($data);

        return ResponseFormatter::success([
            'data' => $supplier
        ], 'Supplier Success');
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
        $supplier = Supplier::find($id);
        return view('page.supplier.form', compact('supplier'));
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
                'nama_supplier' => ['required', 'string', 'max:255'],
                'alamat_supplier' => ['required', 'string', 'max:255'],
                'no_telp' => ['required', 'string', 'max:255'],
            ],
            [
                'nama_supplier.required' => 'Silahkan isi nama supplier',
                'alamat_supplier.required' => 'Silahkan isi alamat supplier',
                'no_telp.required' => 'Silahkan isi no telp',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        $supplier = Supplier::find($id);
        $supplier->update($data);

        return ResponseFormatter::success([
            'data' => $supplier
        ], 'Supplier Success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $supplier = Supplier::find($id);
        $supplier->delete();
        return ResponseFormatter::success([
            'data' => null
        ], 'Deleted');
    }
}
