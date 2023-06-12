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
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('urut');
    }

    public function kategoris()
    {
        return $this->belongsTo(MenuKategori::class, 'menu_kategori_id')->orderBy('urut');
    }



    public function buildMenu($menu, $parentid = 0)
    {
        $result = null;
        foreach ($menu as $item)
            if ($item->parent_id == $parentid) {

                $result .= "<li class='dd-item' data-id='{$item->id}'>
        <div class='dd-handle'>
                    <i class='{$item->class} me-2'></i>
                    <span class='menu-title'>{$item->nama_menu}</span>
        </div>" . $this->buildMenu($menu, $item->id) . "</li>";
            }
        return $result ?  "\n<ol class=\"dd-list\">\n$result</ol>\n" : null;
    }

    // Getter for the HTML menu builder
    public function getHTML($items)
    {
        return $this->buildMenu($items);
    }



}
