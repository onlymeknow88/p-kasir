<?php

namespace App\Helpers;

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
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
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

    public static function format_uang ($angka) {
        return number_format($angka, 0, ',', '.');
    }

    public static function menu($id)
    {
        $menu = DB::table('menus')
            ->join('permissions', 'permissions.menu_id', '=', 'menus.id')
            ->select('permissions.*', 'menus.nama_menu', 'menus.level_menu', 'menus.no_urut', 'menus.link')
            ->where('permissions.role_id', $id)
            // ->where('menu.aktif','N')
            // ->orderBy('menus.no_urut')
            ->get();

        return $menu;
    }

    public static function main_menu()
    {
        $main_menu = DB::table('permissions')->join('menus', 'menus.id', '=', 'permissions.menu_id')
            ->select('menus.*', 'permissions.access', 'permissions.create', 'permissions.edit', 'permissions.delete')
            ->where('permissions.role_id', Auth::user()->role_id)
            ->where('permissions.access', 'Y')
            ->where('menus.level_menu', 'main_menu')->orderBy('menus.no_urut', 'ASC')->get();

        return $main_menu;
    }

    public static function sub_menu()
    {
        $sub_menu = DB::table('permissions')->join('menus', 'menus.id', '=', 'permissions.menu_id')
            ->select('menus.*', 'permissions.access', 'permissions.create', 'permissions.edit', 'permissions.delete')
            ->where('permissions.role_id', Auth::user()->role_id)
            ->where('permissions.access', 'Y')
            ->where('menus.level_menu', 'sub_menu')->orderBy('menus.no_urut', 'ASC')->get();

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

    public static function terbilang ($angka) {
        $angka = abs($angka);
        $baca  = array('', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas');
        $terbilang = '';

        if ($angka < 12) { // 0 - 11
            $terbilang = ' ' . $baca[$angka];
        } elseif ($angka < 20) { // 12 - 19
            $terbilang = ResponseFormatter::terbilang($angka -10) . ' belas';
        } elseif ($angka < 100) { // 20 - 99
            $terbilang = ResponseFormatter::terbilang($angka / 10) . ' puluh' . ResponseFormatter::terbilang($angka % 10);
        } elseif ($angka < 200) { // 100 - 199
            $terbilang = ' seratus' . ResponseFormatter::terbilang($angka -100);
        } elseif ($angka < 1000) { // 200 - 999
            $terbilang = ResponseFormatter::terbilang($angka / 100) . ' ratus' . ResponseFormatter::terbilang($angka % 100);
        } elseif ($angka < 2000) { // 1.000 - 1.999
            $terbilang = ' seribu' . ResponseFormatter::terbilang($angka -1000);
        } elseif ($angka < 1000000) { // 2.000 - 999.999
            $terbilang = ResponseFormatter::terbilang($angka / 1000) . ' ribu' . ResponseFormatter::terbilang($angka % 1000);
        } elseif ($angka < 1000000000) { // 1000000 - 999.999.990
            $terbilang = ResponseFormatter::terbilang($angka / 1000000) . ' juta' . ResponseFormatter::terbilang($angka % 1000000);
        }

        return $terbilang;
    }



}
