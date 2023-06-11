<?php

namespace App\Http\Controllers;

use File;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (request()->ajax()) {
            $query = User::query();

            return DataTables::of($query)
                ->addColumn('role_id', function ($item) {
                    return $item->roles->nama_role;
                })
                ->addColumn('verified', function ($item) {
                    return $item->verified == 1 ? 'Ya' : 'Tidak';
                })
                ->addColumn('avatar', function ($item) {
                    $avatar_path = public_path('assets/img/user/', $item->avatar);
                    if ($item->avatar) {
                        if (file_exists($avatar_path)) {
                            $avatar = $item->avatar;
                        } else {
                            $avatar = 'default.png';
                        }
                    } else {
                        $avatar = 'default.png';
                    }

                    $ignore_avatar = '<img width="60" src="' . asset('assets/img/user/' . $avatar) . '">';

                    return $ignore_avatar;
                })
                ->addColumn('aksi', function ($item) {
                    return '
                    <div class="d-flex justify-content-start">
                        <a class="btn btn-icon color-yellow mr-6 px-2" title="Edit" href="' . route('user.edit', $item->id) . '">
                            <i class="far fa-edit"></i>
                            <span class="form-text-12 fw-bold">Edit</span>
                        </a>
                        <button type="button" class="btn btn-icon color-red mr-6 px-2" title="Delete" onclick="deleteData(`' . route('user.destroy', $item->id) . '`)">
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



        return view('page.user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = new User();
        $role = Role::all();
        return view('page.user.form', compact('user','role'));
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
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'max:255'],
                'verified' => ['required'],
                'status' => ['required'],
                'avatar' => 'nullable|image|mimes:jpeg,png|max:300|dimensions:min_width=100,min_height=100,max_width=100,max_height=100',
                'password' => 'required|min:8|same:ulangi_password',
                'ulangi_password' => 'required|min:6',
            ],
            [
                'avatar.required' => 'Please upload an image.',
                'avatar.image' => 'The file must be an image.',
                'avatar.mimes' => 'Only JPEG and PNG images are allowed.',
                'avatar.max' => 'The image size should not exceed 300KB.',
                'avatar.dimensions' => 'The image dimensions should be exactly 100x100 pixels.',
                'name.required' => 'Silahkan isi nama',
                'username.required' => 'Silahkan isi username',
                'email.required' => 'Silahkan isi email',
                'verified.required' => 'Silahkan pilih',
                'status.required' => 'Silahkan pilih',
                'password.required' => 'Silahkan isi password.',
                'password.min' => 'The password must be at least 8 characters.',
                'password.same' => 'password konfirmasi tidak sama.',
                'ulangi_password.required' => 'Silahkan ulangi password',
                'ulangi_password.min' => 'The password confirmation must be at least 8 characters.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('avatar');
        $data['password'] = Hash::make($request->password);

        $path = 'assets/img/user/';
        $data['avatar'] = null;

        if ($request->hasFile('avatar')) {
            $data['avatar'] = ResponseFormatter::upload_file($path, $request->avatar);
        }

        $user = User::create($data);

        return ResponseFormatter::success([
            'data' => $user
        ], 'User Success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return response()->json(['data' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $role = Role::all();
        return view('page.user.form', compact('user','role'));
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
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'max:255'],
                'verified' => ['required'],
                'status' => ['required'],
                'avatar' => 'nullable|image|mimes:jpeg,png|max:300|dimensions:min_width=100,min_height=100,max_width=130,max_height=130',
            ],
            [
                'avatar.required' => 'Please upload an image.',
                'avatar.image' => 'The file must be an image.',
                'avatar.mimes' => 'Only JPEG and PNG images are allowed.',
                'avatar.max' => 'The image size should not exceed 300KB.',
                'avatar.dimensions' => 'The image dimensions should be exactly 100x100 pixels.',
                'name.required' => 'Silahkan isi nama',
                'username.required' => 'Silahkan isi username',
                'email.required' => 'Silahkan isi email',
                'verified.required' => 'Silahkan pilih',
                'status.required' => 'Silahkan pilih',
            ]
        );

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        $path = 'assets/img/user/';

        $file = $request->avatar;

        $user = User::find($id);

        $new_name = $user->avatar;

        if (!empty($request->avatar_delete_img)) {
            File::delete($path . $user->avatar);
            $new_name = '';
        }

        //old file
        if ($request->hasFile('avatar')) {
            $old_avatar_path = base_path('assets/img/user/' . $user->avatar);
            if (file_exists($old_avatar_path)) {
                unlink($old_avatar_path);
            }
            $new_name =  ResponseFormatter::get_filename(stripslashes($file->getClientOriginalName()), $path);
            $file->move($path, $new_name);
        }

        $data['avatar'] = $new_name;

        $user->update($data);

        return ResponseFormatter::success([
            'data' => $user
        ], 'User Success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        File::delete('assets/img/user/' . $user->avatar);
        $user->delete();
        return ResponseFormatter::success([
            'data' => null
        ], 'File Deleted');
    }

    public function checkUsername(Request $request)
    {
        $username = $request->input('username');

        if (!empty($username)) {
            $response = array('success' => false, 'messages' => array());

            $user = User::where('username', $username);

            $cek = $user->count();

            if ($cek < 1) {
                $response['success'] = true;
                $response['messages'] = "Username Tersedia";
            } else {
                $response['success'] = false;
                $response['messages'] = "Username telah digunakan";
            }
        }

        return response()->json($response);
    }

    public function checkEmail(Request $request)
    {
        $email = $request->input('email');

        if (!empty($email)) {
            $response = array('success' => false, 'messages' => array());

            $user = User::where('email', $email);

            $cek = $user->count();

            if ($cek < 1) {
                $response['success'] = true;
                $response['messages'] = "Email Tersedia";
            } else {
                $response['success'] = false;
                $response['messages'] = "Email telah digunakan";
            }
        }

        return response()->json($response);
    }
}
