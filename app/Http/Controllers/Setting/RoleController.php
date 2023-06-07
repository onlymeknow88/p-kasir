<?php

namespace App\Http\Controllers\Setting;

use App\Models\Menu;
use App\Models\Role;
use App\Models\Permission;
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
                return '<span class="d-flex align-items-center"><i class="' . $item->menu->class . ' me-2"></i>' . $item->menu->nama_menu.'</span>';
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


        $data = $request->except('urut');

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

        $list_permission = Permission::where('role_id', $id)->get();

        foreach ($request->raw as $key => $count) {

            $menu_id    = $request['menu_id'.$count];
            $akses      = $request['akses'.$count] == 'on' ? 'Y':'N';
            $tambah     = $request['tambah'.$count] == 'on' ? 'Y':'N';
            $edit       = $request['edit'.$count] == 'on' ? 'Y':'N';
            $hapus      = $request['hapus'.$count] == 'on' ? 'Y':'N';
            $view       = $request['view'.$count] == 'on' ? 'Y':'N';

            $data = Permission::where('menu_id',$menu_id)->first();
            $data->akses = $akses;
            $data->view = $view;
            $data->tambah = $tambah;
            $data->edit = $edit;
            $data->hapus = $hapus;
            $data->save();

        }

        $data = $request->all();
        $role = Role::findorfail($id);
        $role->update($data);

        return ResponseFormatter::success([
            'data' => $data
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
        $role = Role::findorfail($id);
        $role->delete();
        return ResponseFormatter::success([
            'data' => null
        ], 'Role Deleted');
    }

    public function list_menu(Request $request)
    {
        if (request()->ajax()) {
            $menu = Menu::where('aktif', 'Y')->get();
            $role = Permission::where('role_id', $request->role_id)->get();
            $data = '';

            foreach ($menu as $m) {
                $exist = false;

                foreach ($role as $r) {
                    if ($m->id == $r->menu_id) {
                        $exist = true;
                    }
                }

                if (!$exist) {
                    Permission::create([
                        'role_id' => $request->role_id,
                        'menu_id' => $m->id,
                        'akses' => 'N',
                        'view' => 'N',
                        'tambah' => 'N',
                        'edit' => 'N',
                        'hapus' => 'N',
                    ]);
                }
            }


            $menus = Menu::with('children')->whereNull('parent_id')->where('aktif', 'Y')->get();

            foreach ($menus as $key => $ms) {

                $role = Permission::where('menu_id', '=', $ms->id)->get();
                if (count($role)) {
                    foreach ($role as $r) {
                        $akses = $r->akses == 'Y' ? 'checked' : '';
                        $view = $r->view == 'Y' ? 'checked' : '';
                        $tambah = $r->tambah == 'Y' ? 'checked' : '';
                        $edit = $r->edit == 'Y' ? 'checked' : '';
                        $hapus = $r->hapus == 'Y' ? 'checked' : '';
                    }
                }

                if ($ms->link == '#') {
                    $data .= '<tr>
                                    <input type="hidden" name="menu_id'.$ms->id.'" id="menu_id'.$ms->id.'" value="'.$ms->id.'">
                                    <input type="hidden" name="raw[]" value="'.$ms->id.'">
                                    <td class="fw-bold">' . $ms->nama_menu . '</td>
                                    <td>
                                            <div class="custom-control">
                                                <input name="akses' . $ms->id . '" type="checkbox" class="custom-control-input main"
                                                id="customSwitch akses' . $ms->id . '" ' . $akses . '>
                                                <label class="custom-control-label" for="customSwitch akses' . $ms->id . '"></label>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>';
                    if (count($ms->children)) {
                        $child = $ms->children;
                        $data .= $this->submenu($child);
                    }
                } else {
                    $data .= '<tr>
                                    <input type="hidden" name="menu_id'.$ms->id.'" id="menu_id'.$ms->id.'" value="'.$ms->id.'">
                                    <input type="hidden" name="raw[]" value="'.$ms->id.'">
                                            <td class="fw-bold">' . $ms->nama_menu . '</td>
                                            <td>
                                                <div class="custom-control">
                                                    <input name="akses' . $ms->id . '" type="checkbox" class="custom-control-input main"
                                                    id="customSwitch akses' . $ms->id . '" ' . $akses . '>
                                                    <label class="custom-control-label" for="customSwitch akses' . $ms->id . '"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="custom-control">
                                                    <input name="view' . $ms->id . '" type="checkbox" class="custom-control-input main"
                                                    id="customSwitch view' . $ms->id . '" ' . $view . '>
                                                    <label class="custom-control-label" for="customSwitch view' . $ms->id . '"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="custom-control">
                                                    <input name="tambah' . $ms->id . '" type="checkbox" class="custom-control-input sub"
                                                    id="customSwitch tambah' . $ms->id . '" ' . $tambah . '>
                                                    <label class="custom-control-label" for="customSwitch tambah' . $ms->id . '"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="custom-control">
                                                    <input name="edit' . $ms->id . '" type="checkbox" class="custom-control-input sub"
                                                    id="customSwitch edit' . $ms->id . '" ' . $edit . '>
                                                    <label class="custom-control-label" for="customSwitch edit' . $ms->id . '"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="custom-control">
                                                    <input name="hapus' . $ms->id . '" type="checkbox" class="custom-control-input sub"
                                                    id="customSwitch hapus' . $ms->id . '" ' . $hapus . '>
                                                    <label class="custom-control-label" for="customSwitch hapus' . $ms->id . '"></label>
                                                </div>
                                            </td>
                                        </tr>';
                }
            }
        }

        return response()->json($data);
    }

    private function submenu($child)
    {

        $data = '';
        foreach ($child as $key => $ms) {

            $role = Permission::where('menu_id', '=', $ms->id)->get();
            if (count($role)) {
                foreach ($role as $r) {
                    $akses = $r->akses == 'Y' ? 'checked' : '';
                    $view = $r->view == 'Y' ? 'checked' : '';
                    $tambah = $r->tambah == 'Y' ? 'checked' : '';
                    $edit = $r->edit == 'Y' ? 'checked' : '';
                    $hapus = $r->hapus == 'Y' ? 'checked' : '';
                }
            }

            if ($ms->link == '#') {
                $data .= '<tr>
                <input type="hidden" name="menu_id'.$ms->id.'" id="menu_id'.$ms->id.'" value="'.$ms->id.'">
                                    <input type="hidden" name="raw[]" value="'.$ms->id.'">
                                <td>' . $ms->nama_menu . '</td>
                                <td>
                                        <div class="custom-control">
                                            <input name="akses' . $ms->id . '" type="checkbox" class="custom-control-input main"
                                            id="customSwitch akses' . $ms->id . '" ' . $akses . '>
                                            <label class="custom-control-label" for="customSwitch akses' . $ms->id . '"></label>
                                        </div>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>';
            } else {
                $data .= '<tr>
                <input type="hidden" name="menu_id'.$ms->id.'" id="menu_id'.$ms->id.'" value="'.$ms->id.'">
                                    <input type="hidden" name="raw[]" value="'.$ms->id.'">
                                        <td><span class="ml-15">' . $ms->nama_menu . '</span></td>
                                        <td>
                                            <div class="custom-control">
                                                <input name="akses' . $ms->id . '" type="checkbox" class="custom-control-input main"
                                                id="customSwitch akses' . $ms->id . '" ' . $akses . '>
                                                <label class="custom-control-label" for="customSwitch akses' . $ms->id . '"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="custom-control">
                                                <input name="view' . $ms->id . '" type="checkbox" class="custom-control-input main"
                                                id="customSwitch view' . $ms->id . '" ' . $view . '>
                                                <label class="custom-control-label" for="customSwitch view' . $ms->id . '"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="custom-control">
                                                <input name="tambah' . $ms->id . '" type="checkbox" class="custom-control-input sub"
                                                id="customSwitch tambah' . $ms->id . '" ' . $tambah . '>
                                                <label class="custom-control-label" for="customSwitch tambah' . $ms->id . '"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="custom-control">
                                                <input name="edit' . $ms->id . '" type="checkbox" class="custom-control-input sub"
                                                id="customSwitch edit' . $ms->id . '" ' . $edit . '>
                                                <label class="custom-control-label" for="customSwitch edit' . $ms->id . '"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="custom-control">
                                                <input name="hapus' . $ms->id . '" type="checkbox" class="custom-control-input sub"
                                                id="customSwitch hapus' . $ms->id . '" ' . $hapus . '>
                                                <label class="custom-control-label" for="customSwitch hapus' . $ms->id . '"></label>
                                            </div>
                                        </td>
                                    </tr>';
            }
        }

        return $data;
    }
}
