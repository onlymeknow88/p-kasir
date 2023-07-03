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

    public static function delete_file($path)
    {
        if (file_exists($path)) {
            $unlink = unlink($path);
            if ($unlink) {
                return true;
            }
            return false;
        }

        return true;
    }

    public static function image_dimension($images, $maxw = null, $maxh = null)
    {
        if ($images) {
            $img_size = @getimagesize($images);
            $w = $img_size[0];
            $h = $img_size[1];
            $dim = array('w', 'h');
            foreach ($dim as $val) {
                $max = "max{$val}";
                if (${$val} > ${$max} && ${$max}) {
                    $alt = ($val == 'w') ? 'h' : 'w';
                    $ratio = ${$alt} / ${$val};
                    ${$val} = ${$max};
                    ${$alt} = ${$val} * $ratio;
                }
            }
            return array($w, $h);
        }
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

    public static function nama_bulan()
    {
        return [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
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

    public static function format_date($tgl, $nama_bulan = true) {
        if ($tgl == '0000-00-00 00:00:00' || !$tgl) {
            return false;
        }
        $exp = explode (' ', $tgl);
        $exp_tgl = explode ('-', $exp[0]);
        $bulan = self::nama_bulan();
        return $exp_tgl[2] . ' ' . $bulan[ (int) $exp_tgl[1] ] . ' ' . $exp_tgl[0];
    }

    public static function btn_link($data)
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
		<a href="' . $data['url'] . '" ' . join(' ', $attr) . '>' . $icon . $label . '</a>';
        return $html;
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

    public static function file_type()
    {

        return [

            'text/plain' => ['file_type' => 'document', 'extension' => 'txt'],

            // Image
            'image/jpg'        => ['file_type' => 'image', 'extension' => 'jpg'],
            'image/jpeg'        => ['file_type' => 'image', 'extension' => 'jpg'],
            'image/png'        => ['file_type' => 'image', 'extension' => 'png'],
            'image/bmp'        => ['file_type' => 'image', 'extension' => 'bmp'],
            'image/gif'        => ['file_type' => 'image', 'extension' => 'gif'],

            // Media
            'audio/x-wav'        => ['file_type' => 'audio', 'extension' => 'wav'],
            'audio/flac'        => ['file_type' => 'audio', 'extension' => 'flac'],
            'audio/mpeg'        => ['file_type' => 'audio', 'extension' => 'mp3'],

            'video/mp4'            => ['file_type' => 'video', 'extension' => 'mp4'],
            'video/x-msvideo'     => ['file_type' => 'video', 'extension' => 'avi'],
            'video/quicktime'     => ['file_type' => 'video', 'extension' => 'mov'],
            'video/x-matroska'     => ['file_type' => 'video', 'extension' => 'mkv'],
            'video/x-ms-asf'     => ['file_type' => 'video', 'extension' => 'wmv'],

            // Document
            'application/pdf' => ['file_type' => 'document', 'extension' => 'pdf'],

            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['file_type' => 'document', 'extension' => 'xlsx'], //xlsx
            'application/vnd.ms-excel' => ['file_type' => 'document', 'extension' => 'xls'], // xls
            'application/vnd.oasis.opendocument.spreadsheet' => ['file_type' => 'document', 'extension' => 'ods'], // ods

            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['file_type' => 'document', 'extension' => 'docx'], //docx
            'application/msword' => ['file_type' => 'document', 'extension' => 'doc'], // doc
            'application/vnd.oasis.opendocument.text' => ['file_type' => 'document', 'extension' => 'odt'],
            'text/rtf' => ['file_type' => 'document', 'extension' => 'rtf'],

            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['file_type' => 'document', 'extension' => 'ppt'], // pptx
            'application/vnd.oasis.opendocument.presentation' => ['file_type' => 'document', 'extension' => 'odp'],
            'application/vnd.ms-powerpoint' => ['file_type' => 'document', 'extension' => 'ppt'], //ppt

            // Compression
            'application/x-rar'    => ['file_type' => 'archive', 'extension' => 'rar'],
            'application/zip'    => ['file_type' => 'archive', 'extension' => 'zip'],
            'application/gzip'    => ['file_type' => 'archive', 'extension' => 'gz'],
            'application/x-7z-compressed' => ['file_type' => 'archive', 'extension' => '7z'],

            // Application
            'application/x-msi' => ['file_type' => 'application', 'extension' => 'msi'],
            'application/x-dosexec' => ['file_type' => 'application', 'extension' => 'exe']

        ];
    }
}
