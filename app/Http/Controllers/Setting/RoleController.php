<?php

namespace App\Http\Controllers\Setting;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Role::query();

            return DataTables::of($query)
            ->addColumn('menu_id', function ($item) {
                return $item->menu->nama_menu;
            })
                ->addColumn('aksi', function ($item) {
                    return '
                    <div class="d-flex justify-content-start">
                        <a class="btn btn-icon color-yellow mr-6 px-2" title="Edit" href="' . route('aplikasi.role.edit', $item->id) . '">
                            <i class="far fa-edit"></i>
                            <span class="form-text-12 fw-bold">Edit</span>
                        </a>
                        <button type="button" class="btn btn-icon color-red mr-6 px-2" title="Delete" onclick="deleteData(`' . route('aplikasi.role.destroy', $item->id) . '`)">
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


        return view('page.setting.role.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = new Role();
        $menu = Menu::with('menu_status')->get();
        return view('page.setting.role.form', compact('role', 'menu'));
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
                'nama_role' => ['required', 'string', 'max:255'],
                'judul_role' => ['required', 'string', 'max:255'],
                'keterangan' => ['required', 'string', 'max:255'],
                'menu_id' => ['required'],
            ],
            [
                'nama_role.required' => 'Silahkan isi nama role',
                'judul_role.required' => 'Silahkan isi judul role',
                'keterangan.required' => 'Silahkan isi keterangan',
                'menu_id.required' => 'Silahkan pilih',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = $request->all();
        $role = Role::create($data);

        return ResponseFormatter::success([
            'data' => $role
        ], 'Role Success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        return response()->json(['data' => $role]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::findorfail($id);
        $menu = Menu::with('menu_status')->get();
        return view('page.setting.role.form', compact('role', 'menu'));
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
                'nama_role' => ['required', 'string', 'max:255'],
                'judul_role' => ['required', 'string', 'max:255'],
                'keterangan' => ['required', 'string', 'max:255'],
                'menu_id' => ['required'],
            ],
            [
                'nama_role.required' => 'Silahkan isi nama role',
                'judul_role.required' => 'Silahkan isi judul role',
                'keterangan.required' => 'Silahkan isi keterangan',
                'menu_id.required' => 'Silahkan pilih',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = $request->all();
        $role = Role::findorfail($id);
        $role->update($data);

        return ResponseFormatter::success([
            'data' => $role
        ], 'Role Success');
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
