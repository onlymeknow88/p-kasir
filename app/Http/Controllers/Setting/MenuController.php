<?php

namespace App\Http\Controllers\Setting;

use App\Models\Menu;
use App\Models\Role;
use App\Models\MenuStatus;
use App\Models\MenuKategori;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //menu
        $menuKategori = MenuKategori::all();
        $menus_kategori_list = Menu::where('menu_kategori_id', 1)->orderby('urut', 'asc')->get();
        $menus = new Menu;
        $list_menu = $menus->getHTML($menus_kategori_list);


        $data = [
            'kategori' => $menuKategori,
            'list_menu' => $list_menu,
        ];

        return view('page.setting.menu.index', compact('data'));
    }

    public function buildMenu(Request $request)
    {
        $kategori_id = empty($request->id) ? '' : $request->id;

        $menus = Menu::where('menu_kategori_id', $kategori_id)->orderby('urut', 'asc')->get();
        $menu = new Menu;
        $menu = $menu->buildMenu($menus);

        return response()->json($menu);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!$request->id) {
            $menu = new Menu();
        } else {
            $menu = Menu::find($request->id);
        }

        $menus = Menu::all();
        $menu_status = MenuStatus::all();
        $menuKategori = MenuKategori::all();
        $role = Role::all();

        $data = [
            'menu' => $menus,
            'menu_status' => $menu_status,
            'kategori' => $menuKategori,
            'role' => $role
        ];

        if (!$request->id) {
            $menu = new Menu();
        } else {
            $menu = Menu::find($request->id);
        }
        return view('page.setting.menu.form_menu', compact('menu', 'data'));
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
                'nama_menu' => ['required', 'string', 'max:255'],
                'url' => ['required', 'string', 'max:255'],
                // 'aktif' => ['required'],
                // 'parent_id' => ['required'],
                'use_icon' => ['required'],
                'menu_kategori_id' => ['required'],
                'role_id' => ['required'],
            ],
            [
                'nama_menu.required' => 'Silahkan isi nama menu',
                'url.required' => 'Silahkan isi url',
                // 'aktif.required' => 'Silahkan pilih',
                // 'parent_id.required' => 'Silahkan pilih',
                'use_icon.required' => 'Silahkan pilih',
                'menu_kategori_id.required' => 'Silahkan pilih',
                'role_id.required' => 'Silahkan pilih',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $id = $request->input('id');

        $menu = Menu::updateOrCreate(
            [
                'id' => $id
            ],
            [
                'nama_menu' => $request->input('nama_menu'),
                'url' => $request->input('url'),
                'aktif' => $request->input('aktif') == '' ? 'N' : 'Y',
                'parent_id' => $request->input('parent_id') ?: NULL,
                'link' => $request->input('parent_id') == NULL ? '#' : NULL,
                'class' => $request->input('use_icon') == 1 ? $request->input('icon_class') : NULL,
                'menu_status_id' => $request->input('menu_status_id'),
                'menu_kategori_id' => $request->input('menu_kategori_id') == '' ? NULL : $request->input('menu_kategori_id'),
            ]
        );

        return ResponseFormatter::success([
            'data' => $menu
        ], 'Menu Success');
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

    public function showMenu($id)
    {
        $menu = Menu::with('parent')->find($id);
        $menu['menu_status_id'] = $menu->menu_status->id;
        return response()->json(['data' => $menu]);
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menu = Menu::findorfail($id);
        $menu->delete();
        return ResponseFormatter::success([
            'data' => null
        ], 'Menu Deleted');
    }

    public function getParent(Request $request)
    {
        $search = $request->search;

        if ($search == '') {
            $menus = Menu::orderby('nama_menu', 'asc')->select('id', 'nama_menu')->limit(5)->get();
        } else {
            $menus = Menu::orderby('nama_menu', 'asc')->select('id', 'nama_menu')->where('nama_menu', 'like', '%' . $search . '%')->limit(5)->get();
        }

        $response = array();
        foreach ($menus as $menu) {
            $response[] = array(
                "id" => $menu->id,
                "text" => $menu->nama_menu
            );
        }
        return response()->json($response);
    }

    public function ajaxUpdateUrut(Request $request)
    {
        $menuItems = $request->data;

        $json = json_decode(trim($menuItems), true);

        $result = $this->updateMenuItemsOrder($json, null, $request->menu_kategori_id);

        return ResponseFormatter::success(['data', null], 'Menu Update Urutan Success');

    }

    private function updateMenuItemsOrder($menuItems, $parentID, $menuKategoriId)
    {

        if (empty($menuKategoriId)) {
            $where_id_menu_kategori = '';
        } else {
            $where_id_menu_kategori = $menuKategoriId;
        }

        foreach ($menuItems as $index => $menuItem) {
            $menu = Menu::where('id', $menuItem['id'])->where('menu_kategori_id', $where_id_menu_kategori)->first();
            $menu->urut = $index + 1;
            $menu->parent_id = $parentID;
            $menu->save();

            if (isset($menuItem['children']) && count($menuItem['children']) > 0) {
                $this->updateMenuItemsOrder($menuItem['children'], $menu['id'], $menuKategoriId);
            }
        }
    }
}
