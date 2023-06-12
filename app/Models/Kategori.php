<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = ['nama_kategori','deskripsi','aktif','parent_id','icon','new','urut'];

    public function parent()
    {
        return $this->belongsTo(Kategori::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Kategori::class, 'parent_id')->orderBy('urut');
    }

    public function builKategori($menu, $parentid = 0)
    {
        $result = null;
        foreach ($menu as $item)
            if ($item->parent_id == $parentid) {

                $result .= "<li class='dd-item' data-id='{$item->id}'>
        <div class='dd-handle'>
                    <i class='{$item->icon} me-2'></i>
                    <span class='menu-title'>{$item->nama_kategori}</span>
        </div>" . $this->builKategori($menu, $item->id) . "</li>";
            }
        return $result ?  "\n<ol class=\"dd-list\">\n$result</ol>\n" : null;
    }

    // Getter for the HTML menu builder
    public function getHTML($items)
    {
        return $this->builKategori($items);
    }
}
