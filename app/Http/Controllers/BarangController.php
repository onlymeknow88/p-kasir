<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\Kategori;
use App\Models\BarangStok;
use App\Models\JenisHarga;
use App\Models\BarangHarga;
use Illuminate\Http\Request;
use App\Exports\BarangExport;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
// use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = DB::table('barang')
                ->leftJoin('unit', 'barang.unit_id', '=', 'unit.id')
                ->leftJoin(DB::raw('(SELECT barang_id, SUM(saldo_stok) AS stok
                                    FROM (SELECT barang_id, gudang_id, adjusment_stok AS saldo_stok, "adjusment" AS jenis
                                          FROM barang_adjusment_stok) AS tabel
                                    GROUP BY barang_id) AS tabel_stok'), 'barang.id', '=', 'tabel_stok.barang_id')
                ->select('barang.*', 'tabel_stok.stok');
            // Cache the query results for 5 minutes
            // $results = Cache::remember('query_results', 300, function () use ($query) {
            //     return $query->get();
            // });
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('stok', function ($item) {
                    return $item->stok;
                })
                ->addColumn('aksi', function ($item) {
                    return '
                    <div class="d-flex justify-content-start">
                        <a href="' . route('barang.edit', $item->id) . '" class="btn btn-icon color-yellow mr-6 px-2" title="Edit" >
                            <i class="far fa-edit"></i>
                            <span class="form-text-12 fw-bold">Edit</span>
                        </a>
                        <button type="button" class="btn btn-icon color-red mr-6 px-2" title="Delete" onclick="deleteData(`' . route('barang.destroy', $item->id) . '`)">
                            <i class="far fa-trash-alt text-white"></i>
                            <span class="text-white form-text-12 fw-bold">Hapus</span>
                        </button>
                    </div>
                    ';
                })
                ->rawColumns(['aksi', 'stok'])
                ->escapeColumns([])
                ->make(true);
        }
        return view('page.barang.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create(Request $request)
    // {
    //     // Instantiate a new Barang object.
    //     $barang = new Barang();
    //     // Get the ID from the request.
    //     $id = $request->id;
    //     // Get the stok data.
    //     $data_stok = [];
    //     $stoks = $barang->getStok($id);
    //     if ($stoks) {
    //         foreach ($stoks as $val) {
    //             $data_stok[$val->gudang_id] = $val;
    //         }
    //     }
    //     $stok = $data_stok;
    //     // Get the list of kategori barang.
    //     $kategoris = new Kategori;
    //     $result = $kategoris->getKategori();
    //     $getResult = $kategoris->kategori_list($result);
    //     $list_kategori = $kategoris->buildKategoriList($getResult);
    //     // Get the list of satuan unit.
    //     $satuan_unit = Unit::all();
    //     // Get the list of gudang.
    //     $gudang = Gudang::all();
    //     // Get the latest harga pokok.
    //     $barangHarga = new BarangHarga();
    //     $harga_pokok = $barangHarga->getLatestHargaPokok($id);
    //     // Get the harga jual.
    //     $jenisHarga = new JenisHarga();
    //     $harga_jual = $jenisHarga->getHargaJualByIdBarang($id);
    //     // Return the view with the necessary data.
    //     return view('page.barang.form', compact('barang', 'list_kategori', 'satuan_unit', 'gudang', 'stok', 'harga_pokok', 'harga_jual'));
    // }


    public function create(Request $request)
    {
        // Instantiate a new Barang object.
        $barang = new Barang();
        // Get the ID from the request.
        $id = $request->id;
        // Get the stok data.
        $stok = Cache::remember('stok_data_' . $id, 60, function () use ($barang, $id) {
            return $barang->getStok($id);
        });
        // Get the list of kategori barang.
        $list_kategori = Cache::remember('kategori_data', 60, function () {
            $kategoris = new Kategori;
            $result = $kategoris->getKategori();
            $getResult = $kategoris->kategori_list($result);
            return $kategoris->buildKategoriList($getResult);
        });
        // Get the list of satuan unit.
        $satuan_unit = Cache::remember('satuan_unit_data', 60, function () {
            return Unit::all();
        });
        // Get the list of gudang.
        $gudang = Cache::remember('gudang_data', 60, function () {
            return Gudang::all();
        });
        // Get the latest harga pokok.
        $harga_pokok = Cache::remember('harga_pokok_data_' . $id, 60, function () use ($id) {
            $barangHarga = new BarangHarga();
            return $barangHarga->getLatestHargaPokok($id);
        });
        // Get the harga jual.
        $harga_jual = Cache::remember('harga_jual_data_' . $id, 60, function () use ($id) {
            $jenisHarga = new JenisHarga();
            return $jenisHarga->getHargaJualByIdBarang($id);
        });
        // Return the view with the necessary data.
        return view('page.barang.form', compact('barang', 'list_kategori', 'satuan_unit', 'gudang', 'stok', 'harga_pokok', 'harga_jual'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'kode_barang' => ['required', 'string', 'max:255'],
            'nama_barang' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:255'],
            'unit_id' => ['required'],
            'berat' => ['required'],
            'kategori_id' => ['required'],
            'barcode' => ['required'],
        ]);
        // Check for validation errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Create new Barang with request data
        $data = $request->except('operator', 'gudang_id', 'adjusment', 'harga_pokok', 'harga_jual', 'harga_awal', 'jenis_harga_id', 'adjusment_harga_pokok');

        // dd($data);
        $barang = Barang::create($data);
        // Update user_id_edit and tgl_edit in request data
        $data['user_id_edit'] = Auth::user()->id;
        $data['tgl_edit'] = date('Y-m-d H:i:s');
        // Add adjusment stok data to database
        $data_db = [];
        $userId = Auth::user()->id;
        $now = now();
        foreach ($request->input('adjusment') as $index => $val) {
            if (!$val) {
                continue;
            }
            $val = str_replace('.', '', $val);
            if ($val != 0) {
                $data_db[] = [
                    'barang_id' => $barang->id,
                    'gudang_id' => $request->input('gudang_id')[$index],
                    'adjusment_stok' => $val,
                    'tgl_input' => $now,
                    'user_id_input' => $userId
                ];
            }
        }
        // Insert adjusment stok data to database
        if ($data_db) {
            BarangStok::insert($data_db);
        }
        // Add adjusment harga pokok data to database
        if ($request->input('adjusment_harga_pokok')) {
            $data_db = [
                'barang_id' => $barang->id,
                'harga' => str_replace('.', '', $request->input('harga_pokok')),
                'jenis' => 'harga_pokok',
                'tgl_input' => $now,
                'user_id_input' => $userId
            ];
            BarangHarga::where('barang_id', $barang->id)
                ->where('jenis', 'harga_pokok')
                ->delete();
            BarangHarga::insert($data_db);
        }
        // Add harga jual data to database
        $data_db = [];
        foreach ($request->input('harga_jual') as $index => $val) {
            $val = str_replace('.', '', $val);
            $data_db[] = [
                'barang_id' => $barang->id,
                'jenis_harga_id' => $request->input('jenis_harga_id')[$index],
                'harga' => $val,
                'jenis' => 'harga_jual',
                'tgl_input' => $now,
                'user_id_input' => $userId
            ];
        }
        // Insert harga jual data to database
        if ($data_db) {
            BarangHarga::where('barang_id', $barang->id)
                ->where('jenis', 'harga_jual')
                ->delete();
            BarangHarga::insert($data_db);
        }
        // Return success response with new Barang data
        return ResponseFormatter::success([
            'data' => $barang
        ], 'Success');
    }
    // public function store(Request $request)
    // {
    //     $validator = Validator::make(
    //         $request->all(),
    //         [
    //             'kode_barang' => ['required', 'string', 'max:255'],
    //             'nama_barang' => ['required', 'string', 'max:255'],
    //             'deskripsi' => ['required', 'string', 'max:255'],
    //             'unit_id' => ['required'],
    //             'berat' => ['required'],
    //             'kategori_id' => ['required'],
    //             'barcode' => ['required'],
    //         ]
    //         // ,
    //         // [
    //         //     'nama_menu.required' => 'Silahkan isi nama menu',
    //         //     'url.required' => 'Silahkan isi url',
    //         //     // 'aktif.required' => 'Silahkan pilih',
    //         //     // 'parent_id.required' => 'Silahkan pilih',
    //         //     'use_icon.required' => 'Silahkan pilih',
    //         //     'menu_kategori_id.required' => 'Silahkan pilih',
    //         //     'role_id.required' => 'Silahkan pilih',
    //         // ]
    //     );

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $data = $request->except('berat', 'operator', 'gudang_id', 'adjusment', 'harga_pokok', 'harga_jual', 'harga_awal', 'jenis_harga_id', 'adjusment_harga_pokok');
    //     $barang = Barang::create($data);
    //     $data['user_id_edit'] = Auth::user()->id;
    //     $data['tgl_edit'] = date('Y-m-d H:i:s');


    //     // Adjusment stok
    //     $data_db = [];
    //     foreach ($request->input('adjusment') as $index => $val) {
    //         if (!$val) {
    //             continue;
    //         }

    //         $val = str_replace('.', '', $val);

    //         if ($val != 0) {
    //             $data_db[] = [
    //                 'barang_id' => $barang->id,
    //                 'gudang_id' => $request->input('gudang_id')[$index],
    //                 'adjusment_stok' => $val,
    //                 'tgl_input' => now(),
    //                 'user_id_input' => Auth::user()->id
    //             ];
    //         }
    //     }

    //     if ($data_db) {
    //         DB::table('barang_adjusment_stok')->insert($data_db);
    //     }

    //     if ($request->input('adjusment_harga_pokok')) {
    //         $data_db = [
    //             'barang_id' => $barang->id,
    //             'harga' => str_replace('.', '', $request->input('harga_pokok')),
    //             'jenis' => 'harga_pokok',
    //             'tgl_input' => now(),
    //             'user_id_input' => Auth::user()->id
    //         ];

    //         DB::table('barang_harga')
    //             ->where('barang_id', $barang->id)
    //             ->where('jenis', 'harga_pokok')
    //             ->delete();

    //         DB::table('barang_harga')->insert($data_db);
    //     }

    //     $data_db = [];

    //     foreach ($request->input('harga_jual') as $index => $val) {
    //         $val = str_replace('.', '', $val);

    //         $data_db[] = [
    //             'barang_id' => $barang->id,
    //             'jenis_harga_id' => $request->input('jenis_harga_id')[$index],
    //             'harga' => $val,
    //             'jenis' => 'harga_jual',
    //             'tgl_input' => now(),
    //             'user_id_input' => Auth::user()->id
    //         ];
    //     }

    //     if ($data_db) {
    //         DB::table('barang_harga')
    //             ->where('barang_id', $barang->id)
    //             ->where('jenis', 'harga_jual')
    //             ->delete();

    //         DB::table('barang_harga')->insert($data_db);
    //     }




    //     return ResponseFormatter::success([
    //         'data' => $barang
    //     ], 'Success');
    // }

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
        $barang = Barang::find($id);

        // $id = $request->id;

        //stok
        $data_stok = [];
        $stoks = $barang->getStok($id);
        if ($stoks) {
            foreach ($stoks as $val) {
                $data_stok[$val->gudang_id] = $val;
            }
        }
        $stok = $data_stok;

        //list barang kategori
        $kategoris = new Kategori;
        $result = $kategoris->getKategori();
        $getResult = $kategoris->kategori_list($result);
        $list_kategori = $kategoris->buildKategoriList($getResult);

        //list satuan unit
        $satuan_unit = Unit::all();

        //gudang
        $gudang = Gudang::all();

        //hargaPokok
        $barangHarga = new BarangHarga();
        $harga_pokok = $barangHarga->getLatestHargaPokok($id);

        //hargaJual
        $jenisHarga = new JenisHarga();
        $harga_jual = $jenisHarga->getHargaJualByIdBarang($id);

        // dd($harga_jual);

        return view('page.barang.form', compact('barang', 'list_kategori', 'satuan_unit', 'gudang', 'stok', 'harga_pokok', 'harga_jual'));
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
                'kode_barang' => ['required', 'string', 'max:255'],
                'nama_barang' => ['required', 'string', 'max:255'],
                'deskripsi' => ['required', 'string', 'max:255'],
                'unit_id' => ['required'],
                'berat' => ['required'],
                'kategori_id' => ['required'],
                'barcode' => ['required'],
            ]
            // ,
            // [
            //     'nama_menu.required' => 'Silahkan isi nama menu',
            //     'url.required' => 'Silahkan isi url',
            //     // 'aktif.required' => 'Silahkan pilih',
            //     // 'parent_id.required' => 'Silahkan pilih',
            //     'use_icon.required' => 'Silahkan pilih',
            //     'menu_kategori_id.required' => 'Silahkan pilih',
            //     'role_id.required' => 'Silahkan pilih',
            // ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // $id = $request->input('id');

        $data = $request->except('berat', 'operator', 'gudang_id', 'adjusment', 'harga_pokok', 'harga_jual', 'harga_awal', 'jenis_harga_id', 'adjusment_harga_pokok');
        $data['user_id_edit'] = Auth::user()->id;
        $data['tgl_edit'] = date('Y-m-d H:i:s');


        // Adjusment stok
        $data_db = [];
        foreach ($request->input('adjusment') as $index => $val) {
            if (!$val) {
                continue;
            }

            $val = str_replace('.', '', $val);

            if ($val != 0) {
                $data_db[] = [
                    'barang_id' => $id,
                    'gudang_id' => $request->input('gudang_id')[$index],
                    'adjusment_stok' => $val,
                    'tgl_input' => now(),
                    'user_id_input' => Auth::user()->id
                ];
            }
        }

        if ($data_db) {
            DB::table('barang_adjusment_stok')->insert($data_db);
        }

        if ($request->input('adjusment_harga_pokok')) {
            $data_db = [
                'barang_id' => $id,
                'harga' => str_replace('.', '', $request->input('harga_pokok')),
                'jenis' => 'harga_pokok',
                'tgl_input' => now(),
                'user_id_input' => Auth::user()->id
            ];

            DB::table('barang_harga')
                ->where('barang_id', $id)
                ->where('jenis', 'harga_pokok')
                ->delete();

            DB::table('barang_harga')->insert($data_db);
        }

        $data_db = [];

        foreach ($request->input('harga_jual') as $index => $val) {
            $val = str_replace('.', '', $val);

            $data_db[] = [
                'barang_id' => $id,
                'jenis_harga_id' => $request->input('jenis_harga_id')[$index],
                'harga' => $val,
                'jenis' => 'harga_jual',
                'tgl_input' => now(),
                'user_id_input' => Auth::user()->id
            ];
        }

        if ($data_db) {
            DB::table('barang_harga')
                ->where('barang_id', $id)
                ->where('jenis', 'harga_jual')
                ->delete();

            DB::table('barang_harga')->insert($data_db);
        }

        $barang = Barang::find($id);
        $barang->update($data);


        return ResponseFormatter::success([
            'data' => $barang
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
        $barang = Barang::find($id);
        BarangHarga::where('barang_id', $barang->id)->delete();
        BarangStok::where('barang_id', $barang->id)->delete();
        $barang->delete();

        return ResponseFormatter::success([
            'data' => null
        ], 'Deleted');
    }

    public function ajaxGenerateBarcodeNumber($repeat = false)
    {
        // Generate a random number to add to the current time, or add 0 if $repeat is false
        $add = $repeat ? rand(1, 60) : 0;
        // Get the current time and add the random number to it
        $number = time() + $add;
        // Append '899' to the beginning of the number
        $digit = '899' . substr($number, 0, 9);
        // Split the string into an array of digits
        $split = str_split($digit);
        // Initialize variables for calculating the check digit
        $sum_genab = 0;
        $sum_ganjil = 0;
        // Loop through each digit and add it to the appropriate sum variable
        foreach ($split as $key => &$val) {
            if (($key + 1) % 2) {
                $sum_ganjil = $sum_ganjil + $val;
            } else {
                $sum_genab = $sum_genab + $val;
            }
        }
        // Multiply the sum of even digits by 3
        $sum_genab = $sum_genab * 3;
        // Add the sums together
        $sum = $sum_genab + $sum_ganjil;
        // Calculate the check digit
        $sisa = $sum % 10;
        $check_digit = ($sisa == 0) ? 0 : 10 - $sisa;
        // Append the check digit to the barcode number and return it
        $barcode_number = $digit . $check_digit;
        return $barcode_number;
        // return response()->json(['data' => $barcode_number]);
    }

    public function generateExcel()
    {
        $date = date('d-m-Y');
        return Excel::download(new BarangExport(), 'barang_data - ' . $date . '.xlsx');
    }
}
