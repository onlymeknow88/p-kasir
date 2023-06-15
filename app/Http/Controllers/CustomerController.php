<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Customer::query();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function ($item) {
                    return '
                    <div class="d-flex justify-content-start">
                        <a href="' . route('customer.edit', $item->id) . '" class="btn btn-icon color-yellow mr-6 px-2" title="Edit" >
                            <i class="far fa-edit"></i>
                            <span class="form-text-12 fw-bold">Edit</span>
                        </a>
                        <button type="button" class="btn btn-icon color-red mr-6 px-2" title="Delete" onclick="deleteData(`' . route('customer.destroy', $item->id) . '`)">
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
        return view('page.customer.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customer = new Customer();
        return view('page.customer.form', compact('customer'));
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
                'nama_customer' => ['required', 'string', 'max:255'],
                'alamat_customer' => ['required', 'string', 'max:255'],
                'no_telp' => ['required', 'string', 'max:255'],
            ],
            [
                'nama_customer.required' => 'Silahkan isi nama customer',
                'alamat_customer.required' => 'Silahkan isi alamat customer',
                'no_telp.required' => 'Silahkan isi no telp',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        $customer = Customer::create($data);

        return ResponseFormatter::success([
            'data' => $customer
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
        $customer = Customer::find($id);
        return view('page.customer.form', compact('customer'));
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
                'nama_customer' => ['required', 'string', 'max:255'],
                'alamat_customer' => ['required', 'string', 'max:255'],
                'no_telp' => ['required', 'string', 'max:255'],
            ],
            [
                'nama_customer.required' => 'Silahkan isi nama customer',
                'alamat_customer.required' => 'Silahkan isi alamat customer',
                'no_telp.required' => 'Silahkan isi no telp',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        $customer = Customer::find($id);
        $customer->update($data);

        return ResponseFormatter::success([
            'data' => $customer
        ], 'Success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);
        $customer->delete();
        return ResponseFormatter::success([
            'data' => null
        ], 'Deleted');
    }
}
