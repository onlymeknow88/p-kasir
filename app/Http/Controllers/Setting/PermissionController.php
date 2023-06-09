<?php

namespace App\Http\Controllers\Setting;

use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Permission::query();

            return DataTables::of($query)
                ->addColumn('menu_id', function ($item) {
                    return '<span class="d-flex align-items-center"><i class="' . $item->menus->class . ' me-2"></i>' . $item->menus->nama_menu . '</span>';
                })
                ->addColumn('role_id', function ($item) {
                    return '<span class="badge bg-secondary form-text-12 badge-role px-3 py-2 me-1 mb-1 pe-3">'.$item->roles->judul_role.'</span>';
                })
                ->addColumn('url', function ($item) {
                    return $item->menus->url;
                })
                ->addColumn('link', function ($item) {
                    return $item->menus->link ? '#' : '';
                })
                ->addColumn('aksi', function ($item) {
                    return '
                    <div class="d-flex justify-content-start">
                        <a class="btn btn-icon color-yellow mr-6 px-2" title="Edit" href="' . route('aplikasi.menu-role.edit', $item->id) . '">
                            <i class="far fa-edit"></i>
                            <span class="form-text-12 fw-bold">Edit</span>
                        </a>
                        <button type="button" class="btn btn-icon color-red mr-6 px-2" title="Delete" onclick="deleteData(`' . route('aplikasi.menu-role.destroy', $item->id) . '`)">
                            <i class="far fa-trash-alt text-white"></i>
                            <span class="text-white form-text-12 fw-bold">Hapus</span>
                        </button>
                    </div>
                    ';
                })
                ->rawColumns(['aksi','url','link'])
                ->escapeColumns([])
                ->make(true);
        }
        return view('page.aplikasi.menu-role.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
