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
use LdapRecord\Connection;
use App\Models\LicenseSeat;
use Illuminate\Support\Str;
use Laravolt\Avatar\Avatar;
use App\Models\MenuKategori;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\Auth;
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
    //         'hosts'    => ['ptadaro.com'],
    //         'port'     => 389,
    //         'username' => 'ptadaro\fwivindi',
    //         'password' => 'Suzuran#222',
    //     ]);

    //     return $connection;
    // }

    public static function upload($image, $directory, $file, $filename = "")
    {
        $extensi  = strtolower($file->getClientOriginalExtension());
        $filename = "{$filename}_" . Str::random(10) . ".{$extensi}";

        Storage::disk('public')->putFileAs("/$directory", $file, $filename);

        if (Storage::disk('public')->exists('/' . $directory . '/' . $image)) {
            Storage::disk('public')->delete('/' . $directory . '/' . $image);
        }

        return "$filename";
    }

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

    // public static function menu($id){
    //     $menu = DB::table('menu')
    //     ->join('permission','permission.menu_id','=','menu.id')
    //     ->select('permission.*','menu.nama_menu','menu.urut','menu.link')
    //     // ->where('permission.role_id',$id)
    //     // ->where('menu.aktif','N')
    //     // ->orderBy('menu.no_urut')
    //     ->get();

    //     return $menu;
    // }

    public static function main_menu()
    {
        $main_menu = DB::table('permission')->join('menu', 'menu.id', '=', 'permission.menu_id')
            ->select('menu.*', 'permission.akses', 'permission.tambah', 'permission.edit', 'permission.hapus')
            // ->where('permission.role_id', Auth::user()->role_id)
            ->where('menu.aktif', 'Y')
            // ->where('menu.parent_id', '=', null)
            ->orderBy('menu.urut', 'ASC')->get();

        return $main_menu;
    }

    public static function sub_menu()
    {
        $sub_menu = DB::table('permission')->join('menu', 'menu.id', '=', 'permission.menu_id')
            ->select('menu.*', 'permission.akses', 'permission.tambah', 'permission.edit', 'permission.hapus')
            // ->where('permission.role_id', Auth::user()->role_id)
            ->where('menu.aktif', 'Y')
            ->where('menu.parent_id', '!=', null)
            ->orderBy('menu.urut', 'ASC')->get();

        return $sub_menu;
    }


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
            $terbilang = ResponseFormatter::terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) { // 20 - 99
            $terbilang = ResponseFormatter::terbilang($angka / 10) . ' puluh' . ResponseFormatter::terbilang($angka % 10);
        } elseif ($angka < 200) { // 100 - 199
            $terbilang = ' seratus' . ResponseFormatter::terbilang($angka - 100);
        } elseif ($angka < 1000) { // 200 - 999
            $terbilang = ResponseFormatter::terbilang($angka / 100) . ' ratus' . ResponseFormatter::terbilang($angka % 100);
        } elseif ($angka < 2000) { // 1.000 - 1.999
            $terbilang = ' seribu' . ResponseFormatter::terbilang($angka - 1000);
        } elseif ($angka < 1000000) { // 2.000 - 999.999
            $terbilang = ResponseFormatter::terbilang($angka / 1000) . ' ribu' . ResponseFormatter::terbilang($angka % 1000);
        } elseif ($angka < 1000000000) { // 1000000 - 999.999.990
            $terbilang = ResponseFormatter::terbilang($angka / 1000000) . ' juta' . ResponseFormatter::terbilang($angka % 1000000);
        }

        return $terbilang;
    }

    public static function build_menu($currentPage, $menuKategoriId, $parentid = 0)
    {
        $result = "\n" . '<div class="menu-item accordion" id="menu">' . "\r\n";
        $arr = Menu::where('aktif', 'Y')->get();
        foreach ($arr as $key => $val) {

            // Menu icon
            $menu_icon = '';
            if ($val->class) {
                $menu_icon = '<i class="' . $val->class . ' text-black me-2"></i>';
            }
            $active_link = Request::is($val->url . '/*') ? 'active' : '';
            $active_collpase = Request::is($val->url . '/*') ? 'true' : 'false';

            // menu link
            if ($val->link == '#') {
                $link = $val->link;
                $url = $link . '' . $val->url;
                $collapse = 'data-bs-toggle="collapse" aria-expanded="' . $active_collpase . '"';
                // $buildsubMenu = ResponseFormatter::build_submenu($currentPage, $children, $val->id, $val->url, 'menu');
            } else {
                $link = '';
                $url = '/'.$val->url;
                $collapse = '';
                // $buildsubMenu = '';
            }

            if ($val->parent_id == $parentid && $val->menu_kategori_id == $menuKategoriId) {
                $result .= '<div class="accordion-item">
                            <a href="' . $url . '" class="item-link ' . $active_link . '" ' . $collapse . '>
                                <div class="item-icon">
                                    ' . $menu_icon . '
                                </div>
                                <div class="item-title">
                                    ' . $val->nama_menu . '
                                </div>
                            </a>
                        </div>';

                if (!$val->children->isEmpty()) {

                    $result .= ResponseFormatter::build_submenu($currentPage, $val->children, $val->id, $val->url, 'menu');
                }
            }
        }

        $result .= "</div>\n";
        return $result;
    }

    public static function build_submenu($currentPage, $arr, $parentid = 0, $urlParent, $submenu = false)
    {
        $result = null;

        foreach ($arr as $key => $val) {

            // Menu icon
            $menu_icon = '';
            if ($val->class) {
                $menu_icon = '<i class="' . $val->class . ' text-black me-2"></i>';
            }

            if ($val->aktif == 'Y') {
                // $route_url = route($urlParent.'.'.$val->url.'.index');

                if ($val->link == '#') {
                    $link = $val->link;
                    $route_url = $link . '' . $val->url;
                    $collapse = 'data-bs-toggle="collapse" aria-expanded="false"';
                    // $buildsubMenu = ResponseFormatter::build_submenu($currentPage, $children, $val->id, $val->url, 'menu');
                } else {
                    $link = '';
                    $route_url = route($urlParent.'.'.$val->url.'.index');
                    $collapse = '';
                    // $buildsubMenu = '';
                }

                if($val->parent_id == $parentid) {

                    $result .= '<div class="menu-item" id="menu">
                                    <a href="' . $route_url . '" class="item-link" '.$collapse.'>
                                        <div class="item-icon">
                                            ' . $menu_icon . '
                                        </div>
                                        <div class="item-title">
                                            ' . $val->nama_menu . '
                                        </div>
                                    </a>
                                </div>';
                }

            }
        }

        // return $result;
        return $result ? "\n<div class=\"accordion-collapse collapse\" id='{$urlParent}' data-bs-parent='#{$submenu}'>\n$result</div>\n" : null;
    }
}
