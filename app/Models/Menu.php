<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';

    protected $fillable = ['nama_menu', 'class', 'url', 'parent_id', 'menu_kategori_id', 'aktif', 'new', 'urut', 'menu_status_id'];

    public function menu_status()
    {
        return $this->belongsTo(MenuStatus::class, 'menu_status_id');
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id', 'id');
    }



    public function buildMenu($menu, $parentid = 0)
    {

        // $result = "\n" . '<ol class="dd-list">' . "\r\n";

        // foreach ($menu as $val) {
        //     // Check new
        //     // $new = @$val['new'] == 1 ? '<span class="menu-baru">NEW</span>' : '';
        //     $icon = '';
        //     if ($val->class) {
        //         $icon = '<i class="' . $val->class . '"></i>';
        //     }

        //     if ($val['parent_id'] == $parentid) {
        //         $result .= '<li class="dd-item" data-id="' . $val->id . '"><div class="dd-handle">' . $icon . '<span class="menu-title">' . $val->nama_menu . '</span></div>';
        //     }
        //     // if (key_exists('children', $val))
        //     // {
        //     // 	$menu .= $this->buildMenu($val['children'], ' class="submenu"');
        //     // }
        //     $result .= '"' . $this->buildMenu($menu, $val->id) . '"</li>\n';
        // }
        // $result .= "</ol>\n";
        // return $result;

        $result = null;
        foreach ($menu as $item)
            if ($item->parent_id == $parentid) {

        // $result .= "<li class='dd-item' data-id='{$item->id}'>
        // <div class='dd-handle'>
        //     <div class='d-flex justify-content-between'>
        //         <div class='dd-title'>
        //             <i class='{$item->class} me-2'></i>
        //             <span>{$item->nama_menu}</span>
        //         </div>
        //         <div class='toolbox'>
        //             <a href='#' onclick='editFormMenu(".route('aplikasi.menu.show',$item->id).")'><i
        //                     class='fas fa-pen mx-1 text-green'></i></a>
        //             <a href='#' onclick='deleteMenu()'><i
        //                     class='fas fa-times mx-1 text-red'></i></a>
        //         </div>
        //     </div>
        // </div>". $this->buildMenu($menu, $item->id) . "</li>";
        $result .= "<li class='dd-item' data-id='{$item->id}'>
        <div class='dd-handle'>
                    <i class='{$item->class} me-2'></i>
                    <span>{$item->nama_menu}</span>
        </div>". $this->buildMenu($menu, $item->id) . "</li>";
            }
        return $result ?  "\n<ol class=\"dd-list\">\n$result</ol>\n" : null;
    }

    // Getter for the HTML menu builder
    public function getHTML($items)
    {
        return $this->buildMenu($items);
    }
}
