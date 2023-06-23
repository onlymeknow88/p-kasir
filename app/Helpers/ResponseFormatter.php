<?php

namespace App\Helpers;

use App\Models\Menu;
use App\Models\SettingApp;
use LdapRecord\Connection;
use Illuminate\Support\Str;
use Laravolt\Avatar\Avatar;
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

            if ($val->parent_id == $parentId && $val->menu_kategori_id == $menuKategori) {

                $has_child = $hasChildren ? 'has-children' : '';

                $arrow = $hasChildren ? '<span class="pull-right-container">
                        <i class="fa fa-angle-right arrow"></i>
                    </span>' : '';

                if ($has_child) {
                    $url = '#';
                    $onClick = ' onclick="javascript:void(0)"';
                } else {
                    $onClick = '';
                    $url =  url($val->url);
                }

                //active tree-open highlight current page
                $class_li = [];

                if ($currentPage && url($val->url) === $currentPage) {
                    $class_li[] = 'highlight';
                }

                $class_li[] = self::setActiveMenu(url($val->url), $val->children);

                if ($class_li) {
                    $class_li = ' class="' . join(' ', $class_li) . '"';
                } else {
                    $class_li = '';
                }

                //class has children for active
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

    function setActiveMenu($url, $children)
    {
        $currentUrl = request()->url();

        // Check if the current URL is an exact match
        if ($currentUrl === $url) {
            return 'active';
        }

        // Check if the current URL starts with the given URL
        if (Str::startsWith($currentUrl, $url)) {
            return 'active tree-open';
        }

        // Check if any child menu item is active
        if ($children && self::hasActiveChild($children)) {
            return 'active tree-open';
        }

        return '';
    }

    function hasActiveChild($children)
    {
        foreach ($children as $child) {
            if (self::setActiveMenu(url($child['url']), $child['children'])) {
                return true;
            }
        }

        return false;
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
        $file_name_path = base_path($path, $file_name);
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

    // public static function options(array $attributes, array $options)
    // {
    //     $html = '<select style="width:60px !important;"';
    //     foreach ($attributes as $name => $value) {
    //         $html .= ' ' . $name . '="' . e($value) . '"';
    //     }
    //     $html .= '>';

    //     foreach ($options as $value => $label) {
    //         $html .= '<option value="' . e($value) . '">' . e($label) . '</option>';
    //     }

    //     $html .= '</select>';

    //     return $html;
    // }

    public static function format_ribuan($value)
    {
        if (!$value)
            return 0;
        return number_format((float) $value, 0, ',', '.');
    }

    public static function format_number($value)
    {
        if ($value) {
            $minus = substr($value, 0, 1);
            if ($minus != '-') {
                $minus = '';
            }


            $value = preg_replace('/\D/', '', $value);
        }

        if ($value == 0)
            return 0;

        if ($value == '')
            return '';

        if (!is_numeric($value))
            return '';

        if (empty($value))
            return;

        return $minus . number_format($value, 0, ',', '.');
    }

    function nama_bulan() {
        return [1=> 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    }

    public static function format_tanggal($date, $format = 'dd mmmm yyyy')
    {
        if ($date == '0000-00-00' || $date == '0000-00-00 00:00:00' || $date == '')
            return $date;

        $time = '';
        // Date time
        if (strlen($date) == 19) {
            $exp = explode(' ', $date);
            $date = $exp[0];
            $time = ' ' . $exp[1];
        }

        $format = strtolower($format);
        $new_format = $date;

        list($year, $month, $date) = explode('-', $date);
        if (strpos($format, 'dd') !== false) {
            $new_format = str_replace('dd', $date, $format);
        }

        if (strpos($format, 'mmmm') !== false) {
            $bulan = self::nama_bulan();
            $new_format = str_replace('mmmm', $bulan[($month * 1)], $new_format);
        } else if (strpos($format, 'mm') !== false) {
            $new_format = str_replace('mm', $month, $new_format);
        }

        if (strpos($format, 'yyyy') !== false) {
            $new_format = str_replace('yyyy', $year, $new_format);
        }
        return $new_format . $time;
    }

    public static function btn_action($data = [])
    {

        $html = '<div class="form-inline btn-action-group">';
        $attr = '';
        foreach ($data as $key => $val) {
            if ($key == 'edit') {
                $btn_class = 'btn btn-success btn-xs me-1';
                if (!key_exists('attr', $val)) {

                    $val['attr'] = ['class' => $btn_class];
                }

                foreach ($val['attr'] as $attr_name => $attr_value) {
                    if ($attr_name == 'class') {
                        $attr_value = $btn_class . ' ' . $attr_value;
                    }

                    $attr .= $attr_name . '="' . $attr_value . '"';
                }

                $html .= '<a href="' . $data[$key]['url'] . '" ' . $attr . '>
                            <span class="btn-label-icon"><i class="fa fa-edit pe-1"></i></span> Edit
                        </a>';
            } else if ($key == 'delete') {
                $html .= '<form method="post" action="' . $data[$key]['url'] . '">
                        <button type="submit" data-action="delete-data" data-delete-title="' . $data[$key]['delete-title'] . '" class="btn btn-danger btn-xs">
                            <span class="btn-label-icon"><i class="fa fa-times pe-1"></i></span> Delete
                        </button>
                        <input type="hidden" name="delete" value="delete"/>
                        <input type="hidden" name="id" value="' . $data[$key]['id'] . '"/>
                    </form>';
            } else {

                if (key_exists('attr', $data[$key])) {
                    foreach ($data[$key]['attr'] as $key_attr => $val_attr) {
                        $attr .= $key_attr . '="' . $val_attr . '"';
                    }
                }
                // print_r($attr); die;
                $html .= '<a href="' . $data[$key]['url'] . '" class="btn ' . $data[$key]['btn_class'] . ' btn-xs me-1" ' . $attr . '>
                            <span class="btn-label-icon"><i class="' . $data[$key]['icon'] . '"></i></span>&nbsp;' . $data[$key]['text'] . '
                        </a>';
            }
        }

        $html .= '</div>';
        return $html;
    }

    public static function btn_label($data)
    {
        $attr = [];
        if (key_exists('attr', $data)) {
            foreach ($data['attr'] as $name => $value) {
                if ($name == 'class') {
                    // $value = 'btn-inline ' . $value;
                }
                $attr[] = $name . '="' . $value . '"';
            }
        }

        $label = '';
        if (key_exists('label', $data)) {
            $label = $data['label'];
        }

        $icon = '';
        if (key_exists('icon', $data)) {
            $padding = $label ? ' pe-1' : '';
            $icon = '<span class="btn-label-icon"><i class="' . $data['icon'] . $padding . '"></i></span> ';
        }

        $html = '
		<button  type="button" ' . join(' ', $attr) . '>' . $icon . $label . '</button>';
        return $html;
    }

    public static function options($attr, $data, $selected = '', $print = false)
    {
        if (empty($attr['class'])) {
            $attr['class'] = 'form-select';
        } else {
            $attr['class'] = $attr['class'] . ' form-select';
        }

        foreach ($attr as $key => $val) {
            $attribute[] = $key . '="' . $val . '"';
        }
        $attribute = join(' ', $attribute);

        if ($selected != '') {
            if (!is_array($selected)) {
                $selected = [$selected];
            }
        }

        $result = '
	<select ' . $attribute . '>';
        foreach ($data as $key => $value) {
            $attr_option = '';
            if (is_array($value)) {
                $text = $value['text'];
                if (key_exists('attr', $value)) {
                    $attr_option = ' ';
                    foreach ($value['attr'] as $attr_key => $attr_val) {
                        $attr_option .= $attr_key . '="' . $attr_val . '"';
                    }
                }
            } else {
                $text = $value;
            }

            $option_selected = '';
            if ($selected != '') {
                if (@empty($_REQUEST[$selected[0]])) {
                    if (in_array($key, $selected)) {
                        $option_selected = true;
                    }
                } else {
                    if ($key == $_REQUEST[$selected[0]]) {
                        $option_selected = true;
                    }
                }
            }

            if ($option_selected) {
                $option_selected = ' selected';
            }
            $result .= '<option ' . $attr_option . ' value="' . $key . '"' . $option_selected . '>' . $text . '</option>';
        }

        $result .= '</select>';

        if ($print) {
            echo $result;
        } else {
            return $result;
        }
    }
}
