<?php

namespace App\Http\Controllers;

use File;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
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
            $query = User::with('roles')->select(['id', 'name', 'username', 'email', 'verified', 'avatar', 'role_id']);
            return DataTables::of($query)
                ->addColumn('role_id', function ($user) {
                    return $user->roles->nama_role;
                })
                ->addColumn('verified', function ($user) {
                    return $user->verified ? 'Ya' : 'Tidak';
                })
                ->addColumn('avatar', function ($user) {
                    $avatar = $user->avatar ? asset('assets/img/user/' . $user->avatar) : asset('assets/img/user/default.png');
                    return "<img width='60' src='$avatar'>";
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
                ->rawColumns(['avatar', 'aksi'])
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
        return view('page.user.form', compact('user', 'role'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = $request->id;
        $validator = $this->validateRequest($request, $id);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userData = $request->except('avatar');
        $userData['password'] = Hash::make($request->password);
        $avatarPath = 'assets/img/user/';
        $userData['avatar'] = null;
        $file = Image::make($request->file('avatar')->getPathname());
        // dd($file);
        if ($request->hasFile('avatar')) {
            $userData['avatar'] = $this->uploadFile($avatarPath, $file->resize(100,100));
        }
        $user = User::create($userData);
        return ResponseFormatter::success([
            'data' => $user
        ], 'Success');
    }

    private function validateRequest(Request $request, $id = '')
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
            'verified' => ['required'],
            'status' => ['required'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png', 'max:300'],
        ];
        $messages = [
            'name.required' => __('Please enter a name.'),
            'avatar.image' => 'The file must be an image.',
            'avatar.mimes' => 'Only JPEG and PNG images are allowed.',
            'avatar.max' => 'The image size should not exceed 300KB.',
            'username.required' => __('Please enter a username.'),
            'email.required' => __('Please enter an email address.'),
            'verified.required' => __('Please select an option.'),
            'status.required' => __('Please select an option.'),
        ];
        $sometimesRules = [
            'password' => ['required', 'min:8', 'same:ulangi_password'],
            'ulangi_password' => ['nullable', 'min:6'],
        ];
        $sometimesMessages = [
            'password.required' => __('Please enter a password.'),
            'password.min' => __('The password must be at least 8 characters.'),
            'password.same' => __('The password confirmation does not match.'),
            'ulangi_password.min' => __('The password confirmation must be at least 6 characters.'),
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->sometimes(['password', 'ulangi_password'], $sometimesRules['password'], function () use ($id) {
            return !$id;
        });
        $validator->sometimes(['password', 'ulangi_password'], $sometimesRules['ulangi_password'], function () use ($id) {
            return !$id;
        });
        return $validator;
    }


    private function uploadFile($path, $file)
    {
        return ResponseFormatter::upload_file($path, $file);
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
        return view('page.user.form', compact('user', 'role'));
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

        $validator = $this->validateRequest($request, $id);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
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
