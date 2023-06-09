<?php

namespace App\Helpers;

use App\Models\Menu;
use App\Models\Asset;
use App\Models\Company;
use App\Models\General;
use Milon\Barcode\DNS1D;
use App\Models\Accessory;
use App\Models\ActionLog;
use App\Models\Component;
use App\Models\SettingApp;
use LdapRecord\Connection;
use App\Models\LicenseSeat;
use Illuminate\Support\Str;
use Laravolt\Avatar\Avatar;
use App\Models\MenuKategori;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class ResponseFormatter
{
    /**
     * API Response
     *
     * @var array
     */
    protected static $response = [
        'meta' => [
            'code' => 200,
            'status' => 'success',
            'message' => null,
        ],
        'result' => null,
    ];

    /**
     * Give success response.
     */
    public static function success($data = null, $message = null)
    {
        self::$response['meta']['message'] = $message;
        self::$response['result'] = $data;

        return response()->json(self::$response, self::$response['meta']['code']);
    }

    /**
     * Give error response.
     */
    public static function error($message = null, $code = 400)
    {
        self::$response['meta']['status'] = 'error';
        self::$response['meta']['code'] = $code;
        self::$response['meta']['message'] = $message;

        return response()->json(self::$response, self::$response['meta']['code']);
    }

    // public static function connectLdap()
    // {
    //     $connection = new Connection([
    //         'hosts'    => [''],
    //         'port'     => 389,
    //         'username' => '',
    //         'password' => '',
    //     ]);

    //     return $connection;
    // }

    // public static function upload($image, $directory, $file, $filename = "")
    // {
    //     $extensi  = strtolower($file->getClientOriginalExtension());
    //     $filename = "{$filename}_" . Str::random(10) . ".{$extensi}";

    //     Storage::disk('public')->putFileAs("/$directory", $file, $filename);

    //     if (Storage::disk('public')->exists('/' . $directory . '/' . $image)) {
    //         Storage::disk('public')->delete('/' . $directory . '/' . $image);
    //     }

    //     return "$filename";
    // }

    // public static function downloader($filename, $disk = 'default')
    // {
    //     if ($disk == 'default') {
    //         $disk = config('filesystems.default');
    //     }
    //     switch (config("filesystems.disks.$disk.driver")) {
    //         case 'local':
    //             return response()->download(Storage::disk($disk)->path($filename)); //works for PRIVATE or public?!

    //         case 'public':
    //             return response()->download(Storage::disk($disk)->path($filename)); //works for PRIVATE or public?!

    //         case 's3':
    //             return redirect()->away(Storage::disk($disk)->temporaryUrl($filename, now()->addMinutes(5))); //works for private or public, I guess?

    //         default:
    //             return Storage::disk($disk)->download($filename);
    //     }
    // }

    public static function getProfilePicture($name)
    {
        $initials = \Avatar::create($name)->toBase64();
        return $initials;
    }

    public static function format_uang($angka)
    {
        return number_format($angka, 0, ',', '.');
    }

    public static function menuKategori()
    {
        $menuKategori =  DB::table('menu_kategori')
            // ->where('permission.role_id', Auth::user()->role_id)
            ->where('menu_kategori.aktif', 'Y')
            ->orderBy('urut', 'ASC')
            ->get();


        return $menuKategori;
    }

    public static function list_menu()
    {
        $menu = DB::table('menu')
            ->join('permission', 'permission.menu_id', '=', 'menu.id')
            ->select('permission.*', 'menu.nama_menu', 'menu.urut', 'menu.link')
            // ->where('permission.role_id',$id)
            // ->where('menu.aktif','N')
            ->orderBy('menu.urut')
            ->get();

        return $menu;
    }

    public static function menu()
    {
        $arr = Menu::whereNull('parent_id')->with('children')->where('aktif', 'Y')->orderBy('urut')->get();

        return $arr;
    }

    // public static function menu($roleid)
    // {
    //     $menu = DB::table('menu')
    //         ->join('permission', 'permission.menu_id', '=', 'menu.id')
    //         ->select('permission.*', 'menu.nama_menu', 'menu.parent_id', 'menu.aktif', 'menu.urut', 'menu.link', 'menu.menu_kategori_id')
    //         ->where('permission.role_id', $roleid)
    //         // ->where('menu.aktif','N')
    //         ->orderBy('menu.urut')
    //         ->get();

    //     return $menu;
    // }

    public static function tanggal_indonesia($tgl, $tampil_hari = false)
    {
        $nama_hari  = array(
            'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu'
        );
        $nama_bulan = array(
            1 =>
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        );

        $tahun   = substr($tgl, 0, 4);
        $bulan   = $nama_bulan[(int) substr($tgl, 5, 2)];
        $tanggal = substr($tgl, 8, 2);
        $text    = '';

        if ($tampil_hari) {
            $urutan_hari = date('w', mktime(0, 0, 0, substr($tgl, 5, 2), $tanggal, $tahun));
            $hari        = $nama_hari[$urutan_hari];
            $text       .= "$hari, $tanggal $bulan $tahun";
        } else {
            $text       .= "$tanggal $bulan $tahun";
        }

        return $text;
    }

    public static function tambah_nol_didepan($value, $threshold = null)
    {
        return sprintf("%0" . $threshold . "s", $value);
    }

    public static function terbilang($angka)
    {
        $angka = abs($angka);
        $baca  = array('', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas');
        $terbilang = '';

        if ($angka < 12) { // 0 - 11
            $terbilang = ' ' . $baca[$angka];
        } elseif ($angka < 20) { // 12 - 19
            $terbilang = self::terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) { // 20 - 99
            $terbilang = self::terbilang($angka / 10) . ' puluh' . self::terbilang($angka % 10);
        } elseif ($angka < 200) { // 100 - 199
            $terbilang = ' seratus' . self::terbilang($angka - 100);
        } elseif ($angka < 1000) { // 200 - 999
            $terbilang = self::terbilang($angka / 100) . ' ratus' . self::terbilang($angka % 100);
        } elseif ($angka < 2000) { // 1.000 - 1.999
            $terbilang = ' seribu' . self::terbilang($angka - 1000);
        } elseif ($angka < 1000000) { // 2.000 - 999.999
            $terbilang = self::terbilang($angka / 1000) . ' ribu' . self::terbilang($angka % 1000);
        } elseif ($angka < 1000000000) { // 1000000 - 999.999.990
            $terbilang = self::terbilang($angka / 1000000) . ' juta' . self::terbilang($angka % 1000000);
        }

        return $terbilang;
    }

    public static function build_menu($arr, $currentPage, $menuKategori = null, $parentId = 0, $submenu = false)
    {
        $menu = "\n" . '<ul' . $submenu . '>' . "\r\n";
        foreach ($arr as $key => $val) {

            $hasChildren = self::hasChildren($val['children'], $val->id);
            $isOpen = self::isChildActive($val['children'], $val->item, $currentPage) ? 'tree-open' : '';
            // dd($a);

            $class_li = [];

            // dd($currentPage);]



            if ($val->parent_id == $parentId && $val->menu_kategori_id == $menuKategori) {

                $has_child = $hasChildren ? 'has-children' : '';

                $arrow = $hasChildren ? '<span class="pull-right-container">
                        <i class="fa fa-angle-left arrow"></i>
                    </span>' : '';

                if ($has_child) {
                    $url = '#';
                    $onClick = ' onclick="javascript:void(0)"';
                } else {
                    $onClick = '';
                    $url =  url($val->url);
                }

                    if ($currentPage && url($val->url) === $currentPage) {
                        $class_li[] = 'highlight';
                    }

                if ($currentPage && url($val->url) === $currentPage) {
                    $class_li[] = 'highlight';
                }

                if($val->link == '#'){
                    if (Request::is($val->url.'/*') || Request::is('aplikasi/setting/*')) {
                        $class_li[] = 'active tree-open';
                    }
                }



                if ($class_li) {
                    $class_li = ' class="' . join(' ', $class_li) . '"';
                } else {
                    $class_li = '';
                }


                $class_a = ['depth-' . $val['id']];
                if ($has_child) {
                    $class_a[] = 'has-children';
                }

                $class_a = ' class="' . join(' ', $class_a) . '"';

                $menu_icon = '';
                if ($val->class) {
                    $menu_icon = '<i class="sidebar-menu-icon ' . $val->class . '"></i>';
                }

                $menu .= '<li' . $class_li . '>
                            <a ' . $class_a . ' href="' . $url . '"' . $onClick . '>' .
                    '<span class="menu-item">' .
                    $menu_icon .
                    '<span class="text">' . $val->nama_menu . '</span>' .
                    '</span>' .
                    $arrow .
                    '</a>';
                if ($hasChildren) {
                    $menu .= self::build_menu($val['children'], $currentPage,  $menuKategori, $val->id, ' class="submenu"');
                }
                $menu .= "</li>\n";
            }
        }
        $menu .= "</ul>\n";
        return $menu;
    }

    function hasChildren($menuItems, $parentId)
    {
        foreach ($menuItems as $menuItem) {
            if ($menuItem['parent_id'] == $parentId) {
                return true;
            }
        }
        return false;
    }

    function isChildActive($menuItems, $parentId, $activeUrl)
    {
        foreach ($menuItems as $menuItem) {

            if ($menuItem['parent_id'] == $parentId && url($menuItem['url']) == $activeUrl) {
                return true;
            }
            if (self::hasChildren($menuItems, $menuItem['id']) && self::isChildActive($menuItems, $menuItem['id'], $activeUrl)) {
                return true;
            }
        }
        return false;
    }

    public static function settingApp()
    {
        $data = [];
        $setting_app = SettingApp::where('type', 'app')->get()->toArray();

        foreach ($setting_app as $val) {
            $data[$val['param']] = $val['value'];
        }

        return $data;
    }

    public static function get_filename($file_name, $path)
    {
        $file_name_path = public_path($path, $file_name);
        // echo '-' . $file_name_path . '-';
        if ($file_name != "" && file_exists($file_name_path)) {
            $file_ext = strrchr($file_name, '.');
            $file_basename = substr($file_name, 0, strripos($file_name, '.'));
            $num = 1;
            while (file_exists($file_name_path)) {
                $file_name = $file_basename . "_$num" . $file_ext;
                $num++;
                $file_name_path = $path . $file_name;
            }

            return $file_name;
        }
        return $file_name;
    }

    public static function upload_file($path, $file)
    {
        $new_name =  self::get_filename(stripslashes($file->getClientOriginalName()), $path);
        $move = $file->move($path, $new_name);
        if ($move)
            return $new_name;
        else
            return false;
    }

    public static function set_value($field_name, $default = '')
    {
        $request = array_merge($_GET, $_POST);
        $search = $field_name;

        // If Array
        $is_array = false;
        if (strpos($search, '[')) {
            $is_array = true;
            $exp = explode('[', $field_name);
            $field_name = $exp[0];
        }

        if (isset($request[$field_name])) {
            if ($is_array) {
                $exp_close = explode(']', $exp[1]);
                $index = $exp_close[0];
                return $request[$field_name][$index];
            }
            return $request[$field_name];
        }
        return $default;
    }
}
