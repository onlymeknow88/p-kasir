<?php

namespace App\Http\Controllers\Setting;

use App\Models\Menu;
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
        $menuKategori = MenuKategori::all();
        //menu
        $menus_kategori_list = Menu::where('menu_kategori_id', 1)->orderby('urut', 'asc')->get();
        $menu = new Menu;
        $menu = $menu->getHTML($menus_kategori_list);

        $children_menu = Menu::all();


        return view('page.setting.menu.index', compact('menuKategori','menu','children_menu'));
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
                'nama_menu' => ['required', 'string', 'max:255'],
                'url' => ['required', 'string', 'max:255'],
                // 'aktif' => ['required'],
                // 'parent_id' => ['required'],
                'use_icon' => ['required'],
                'menu_kategori_id' => ['required'],
            ],
            [
                'nama_menu.required' => 'Silahkan isi nama menu',
                'url.required' => 'Silahkan isi url',
                // 'aktif.required' => 'Silahkan pilih',
                // 'parent_id.required' => 'Silahkan pilih',
                'use_icon.required' => 'Silahkan pilih',
                'menu_kategori_id.required' => 'Silahkan pilih',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $menu = new Menu();

        $menu->nama_menu = $request->input('nama_menu');
        $menu->url = $request->input('url');

        if (empty($request->input('aktif'))) {
            $aktif = 'N';
        } else {
            $aktif = 'Y';
        }

        $menu->aktif = $aktif;

        $menu->parent_id = $request->input('parent_id') ?: NULL;
        $menu->class = $request->input('icon_class');

        if ($request->input('menu_kategori_id') == '') {
            $menu_kategori_id = NULL;
        } else {
            $menu_kategori_id = $request->input('menu_kategori_id');
        }
        $menu->menu_kategori_id = $menu_kategori_id;
        // $menu->save();

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
        $validator = Validator::make(
            $request->all(),
            [
                'nama_menu' => ['required', 'string', 'max:255'],
                'url' => ['required', 'string', 'max:255'],
                'aktif' => ['required'],
                // 'parent_id' => ['required'],
                'use_icon' => ['required'],
                'menu_kategori_id' => ['required'],
            ],
            [
                'nama_menu.required' => 'Silahkan isi nama menu',
                'url.required' => 'Silahkan isi url',
                'aktif.required' => 'Silahkan pilih',
                // 'parent_id.required' => 'Silahkan pilih',
                'use_icon.required' => 'Silahkan pilih',
                'menu_kategori_id.required' => 'Silahkan pilih',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $menu = new Menu();

        $menu->nama_menu = $request->input('nama_menu');
        $menu->url = $request->input('url');

        if (empty($request->input('aktif'))) {
            $aktif = 'N';
        } else {
            $aktif = 'Y';
        }

        $menu->aktif = $aktif;

        $menu->parent_id = $request->input('parent_id');
        $menu->class = $request->input('icon_class');

        if ($request->input('menu_kategori_id') == '') {
            $menu_kategori_id = NULL;
        } else {
            $menu_kategori_id = $request->input('menu_kategori_id');
        }
        $menu->menu_kategori_id = $menu_kategori_id;
        // $menu->save();

        return ResponseFormatter::success([
            'data' => $menu
        ], 'Menu Success');
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
        ],'Menu Deleted');
    }

    public function getParent(Request $request)
    {
        $search = $request->search;

        if($search == ''){
            $menus = Menu::orderby('nama_menu','asc')->select('id','nama_menu')->limit(5)->get();
         }else{
            $menus = Menu::orderby('nama_menu','asc')->select('id','nama_menu')->where('nama_menu', 'like', '%' .$search . '%')->limit(5)->get();
         }

         $response = array();
         foreach($menus as $menu){
            $response[] = array(
                 "id"=>$menu->id,
                 "text"=>$menu->nama_menu
            );
         }
         return response()->json($response);
    }
}
