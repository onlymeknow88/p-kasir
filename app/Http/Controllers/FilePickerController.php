<?php

namespace App\Http\Controllers;

use App\Models\FilePicker;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class FilePickerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getData($request, $item_per_page)
    {
        $list_file_type = ResponseFormatter::file_type();
        // dd($list_file_type);
        $where = ' WHERE 1 = 1';
        $result['filter_tgl'] = [];

        if (!empty($request->input('id_file_picker'))) {
            $where .= ' AND id = ' . $request->input('id_file_picker');
        } else {
            if (!empty($request->input('filter_file'))) {
                $split = explode(' ', $request->input('filter_file'));
                $list_filter = [];


                foreach ($split as $filter) {
                    $filter = trim($filter);
                    if (!$filter)
                        continue;

                    $list_mime = [];
                    foreach ($list_file_type as $mime => $val) {
                        if ($val['file_type'] == $filter) {
                            $list_mime[] = $mime;
                        }
                    }

                    if ($list_mime) {
                        $list_filter[] = 'mime_type IN ("' . join('","', $list_mime) . '")';
                    }
                }

                if ($list_filter) {
                    $where .= ' AND (' . join(' OR ', $list_filter) . ')';
                }
            }
            // Date Options
            $tanggal = DB::table('file_picker')
                ->select(DB::raw('DATE_FORMAT(tgl_upload,"%Y-%m") AS bulan'))
                ->groupBy('bulan')
                ->orderBy('bulan', 'DESC')
                ->get()
                ->toArray();

            $nama_bulan = ResponseFormatter::nama_bulan();
            foreach ($tanggal as $val) {
                $exp = explode('-', $val->bulan);
                $result['filter_tgl'][$val->bulan] = $nama_bulan[$exp[1] * 1] . ' ' . $exp[0];
            }

            // Filter Tgl
            if (!empty($request->input('filter_tgl'))) {
                $where .= ' AND tgl_upload LIKE "' . $request->input('filter_tgl') . '%"';
            }

            // Filter Search
            if (!empty($request->input('q')) && trim($request->input('q')) != '') {
                $where .= ' AND (title LIKE "%' . $request->input('q') . '%" OR nama_file LIKE "%' . $request->input('q') . '%")';
            }
        }

        $page = $request->input('page', 1);
        $limit = $item_per_page * ($page - 1) . ', ' . $item_per_page;

        $sql = 'SELECT * FROM file_picker ' . $where . ' ORDER BY tgl_upload DESC LIMIT ' . $limit;
        $result['data'] = DB::select($sql);



        $sql = 'SELECT COUNT(*) AS total_item FROM file_picker ' . $where;
        $query = DB::selectOne($sql);
        // dd($result);
        $total_item = $query->total_item;
        $result['total_item'] = $total_item;

        $jml_data = count($result['data']);
        $loaded_item = $jml_data < $item_per_page ? $jml_data : $item_per_page;
        $result['loaded_item'] = ($item_per_page * ($page - 1)) + count($result['data']);

        foreach ($result['data'] as $key => $val) {
            $meta_file = json_decode($val->meta_file, true);
            // dd($val->nama_file);
            $properties = $this->getFileProperties($val->mime_type, $val->nama_file, $meta_file);
            $result['data'][$key] = array_merge((array) $result['data'][$key], $properties);
        }

        // dd($result);
        return ['result' => $result, 'total_item' => $total_item, 'loaded_item' => $loaded_item];
    }

    private function getFileProperties($mime, $file_name, $meta_file)
    {
        $list_file_type = ResponseFormatter::file_type();

        $extension_color = $extension = '';
        $mime_image = ['image/png', 'image/jpeg', 'image/bmp', 'image/gif'];

        $file_exists = true;
        // echo $config['filepicker_upload_path'] . $file_name; die;
        // echo $config->uploadPath; die;
        if (file_exists(public_path('assets/files/upload/' . $file_name))) {
            $result['file_exists']['original'] = 'found';
        } else {
            $file_exists = false;
            $result['file_exists']['original'] = 'not_found';
        }

        if (in_array($mime, $mime_image)) {

            $thumbnail_file = $file_name;
            if (key_exists('thumbnail', $meta_file)) {
                $thumbnail = $meta_file['thumbnail'];
                foreach ($thumbnail as $size => $val) {
                    if (file_exists(public_path('assets/files/upload/' . $val['filename']))) {
                        $result['file_exists']['thumbnail'][$size] = 'found';
                    } else {
                        $file_exists = false;
                        $result['file_exists']['thumbnail'][$size] = 'not_found';
                    }
                }

                if (key_exists('small', $thumbnail)) {
                    $thumbnail_file = $thumbnail['small']['filename'];
                }
            }


            $thumbnail_url = asset('assets/files/upload/' . $thumbnail_file);

            $file_type = 'image';
        } else {

            $pathinfo = pathinfo($file_name);
            // dd($asset);
            $extension = $pathinfo['extension'];

            $file_icon = 'file';
            $file_type = 'non_image';

            if (key_exists($mime, $list_file_type)) {
                $file_icon = $list_file_type[$mime]['extension'];
                $file_type = $list_file_type[$mime]['file_type'];
            } else {

                foreach ($list_file_type as $val) {
                    if ($val['extension'] == $extension) {
                        $file_icon = strtolower($extension);
                        $file_type = $val['file_type'];
                    }
                }
            }

            $thumbnail_url = asset('assets/files/upload/' . $file_icon . '.png');
        }

        if (!$file_exists) {
            $thumbnail_url = asset('assets/files/upload/file_not_found.png');
        }


        if (!key_exists('thumbnail', $result['file_exists'])) {
            $result['file_exists']['thumbnail'] = [];
        }

        if ($file_exists) {
            $result['file_not_found'] = 'false';
        } else {
            $result['file_not_found'] = 'true';
        }

        $result['file_type'] = $file_type;
        $result['url'] = asset('assets/files/upload/' .  $file_name);
        $result['thumbnail']['url'] = $thumbnail_url;
        $result['thumbnail']['extension_name'] = $extension;

        return $result;
    }

    public function index(Request $request)
    {

        $item_per_page = 50;
        $item_per_page = !empty($request->input('item_per_page')) ? $request->input('item_per_page') : $item_per_page;

        $load_item = $this->getData($request, $item_per_page);
        // dd($load_item['result']);
        if (!empty($request->input('ajax'))) {
            return response()->json($load_item['result']);
            exit();
        }

        // return view('page.filepicker.index');
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

    public function ajaxDeleteFile(Request $request)
    {
        $id_files = json_decode($request->id, true);

        // dd($id);

        if (!is_array($id_files)) {
            if ($id_files) {
                $id_files = [$id_files];
            } else {
                $id_files = [$request->id];
            }
        }

        $error = [];
        foreach ($id_files as $id_file) {
            // $sql = 'SELECT * FROM file_picker WHERE id_file_picker = ?';
            $file = FilePicker::where('id', $id_files)->first();

            if (!$file) {
                $error[] = 'File tidak ditemukan';
            } else {
                $delete = DB::table('file_picker')->where('id', $id_file)->delete();
                if ($delete) {
                    $meta = json_decode($file['meta_file'], true);

                    $dir = trim(public_path('assets/files/upload'), '/');
                    $dir = trim($dir, '\\');
                    $dir = $dir . '/';

                    // Main File
                    if (file_exists($dir . $file['nama_file'])) {
                        $unlink = ResponseFormatter::delete_file($dir . $file['nama_file']);
                        if (!$unlink) {
                            $error[] = 'Gagal menghapus file: ' . $val['filename'];
                        }
                    }

                    // Thumbnail
                    if (key_exists('thumbnail', $meta)) {
                        foreach ($meta['thumbnail'] as $val) {
                            if (file_exists($dir . $val['filename'])) {
                                $unlink = ResponseFormatter::delete_file($dir . $val['filename']);
                                if (!$unlink) {
                                    $error[] = 'Gagal menghapus file: ' . $val['filename'];
                                }
                            }
                        }
                    }
                } else {
                    $error[] = 'Gagal menghapus data database file ID: ' . $id_file;
                }
            }
        }
        if ($error) {
			$result['status'] = 'error';
			$result['message'] = '<ul><li>' . join('</li></li>', $error) . '</li></ul>';
		} else {
			$result['status'] = 'ok';
			$result['message'] = 'Data berhasil dihapus';
		}

		return $result;
    }

    public function ajaxUpdateFile(Request $request)
    {
        $update = FilePicker::where('id', $request->input('id'))
            ->update([$request->input('name') => $request->input('value')]);
        if ($update)
            return response()->json(['status' => 'ok']);
        else
            return response()->json(['status' => 'error']);

        exit;
        // return $update;
    }

    public function ajaxUploadFile(Request $request)
    {
        $nama_bulan = ResponseFormatter::nama_bulan();
        $list_file_type = ResponseFormatter::file_type();

        $thumbnail = [
            'small' => ['w' => 250, 'h' => 250],
            'medium' => ['w' => 450, 'h' => 450]
        ];

        if ($request->hasFile('file')) {
            if (file_exists(public_path('assets/files/upload/')) && is_dir(public_path('assets/files/upload/'))) {
                if (!is_writable(public_path('assets/files/upload/'))) {
                    $result = [
                        'status' => 'error',
                        'message' => 'Tidak dapat menulis file ke folder'
                    ];
                } else {
                    $file = $request->file('file');
                    $getSize = $file->getSize();
                    // dd($file->getSize());
                    $new_name = ResponseFormatter::upload_file(public_path('assets/files/upload/'), $file);

                    if ($new_name) {
                        $meta_file = [];
                        $mime_image = ['image/png', 'image/jpeg', 'image/bmp', 'image/gif'];
                        $current_mime_type = $file->getClientMimeType();

                        if (in_array($current_mime_type, $mime_image)) {
                            $img_size = @getimagesize(public_path('assets/files/upload/' . $new_name));


                            $meta_file['default'] = [
                                'width' => $img_size[0],
                                'height' => $img_size[1],
                                'size' => $getSize
                            ];

                            foreach ($thumbnail as $size => $dim) {
                                if ($img_size[0] > $dim['w'] || $img_size[1] > $dim['h']) {
                                    $img_dim = ResponseFormatter::image_dimension(public_path('assets/files/upload/' . $new_name), $dim['w'], $dim['h']);
                                    $img_width = ceil($img_dim[0]);
                                    $img_height = ceil($img_dim[1]);

                                    $width = $height = null;
                                    if ($img_width >= $dim['w']) {
                                        $width = $dim['w'];
                                    } elseif ($img_height >= $dim['h']) {
                                        $height = $dim['h'];
                                    }
                                    $image = Image::make(public_path('assets/files/upload/' . $new_name));
                                    $image->resize($width, $height, function ($constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });

                                    $name_path = pathinfo($new_name);
                                    $thumb_name = $name_path['filename'] . '_' . $size . '.' . $name_path['extension'];
                                    $image->save(public_path('assets/files/upload/' . $thumb_name), 97);

                                    $thumb_dim = getimagesize(public_path('assets/files/upload/' . $thumb_name));
                                    $meta_file['thumbnail'][$size] = [
                                        'filename' => $thumb_name,
                                        'width' => $thumb_dim[0],
                                        'height' => $thumb_dim[1],
                                        'size' => filesize(public_path('assets/files/upload/' . $thumb_name))
                                    ];
                                }
                            }
                        }
                        $data_db = [
                            'nama_file' => $new_name,
                            'mime_type' => $current_mime_type,
                            'size' => $getSize,
                            'tgl_upload' => date('Y-m-d H:i:s'),
                            'user_id_upload' => Auth::user()->id,
                            'meta_file' => json_encode($meta_file)
                        ];

                        $insert = DB::table('file_picker')->insert($data_db);
                        $id_file_picker = DB::getPdo()->lastInsertId();

                        $file_info = $data_db;
                        $file_info['bulan_upload'][date('Y-m')] = $nama_bulan[date('n')] . ' ' . date('Y');
                        $file_info['id'] = $id_file_picker;
                        $result = $this->getFileProperties($current_mime_type, $new_name, $meta_file);
                        $file_info = array_merge($file_info, $result);

                        // dd($file_info);

                        $result = [
                            'status' => 'success',
                            'message' => 'File berhasil diupload.',
                            'file_info' => $file_info
                        ];
                    } else {
                        $result = [
                            'status' => 'error',
                            'message' => 'System error'
                        ];
                    }
                }
            } else {
                $result = [
                    'status' => 'error',
                    'message' => 'Folder ' . public_path('assets/files/upload/') . ' tidak ditemukan'
                ];
            }
        } else {
            $result = [
                'status' => 'error',
                'message' => 'file empty'
            ];
        }

        return $result;
    }
}
