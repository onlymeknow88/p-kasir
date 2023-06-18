<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = ['nama_kategori', 'deskripsi', 'aktif', 'parent_id', 'icon', 'new', 'urut'];

    public function parent()
    {
        return $this->belongsTo(Kategori::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Kategori::class, 'parent_id')->orderBy('urut');
    }

    public function buildKategoriList($arr, $id_parent = '', &$result = [])
    {

        foreach ($arr as $key => $val) {
            $result[$val['id']] = [
                'attr' => ['data-parent' => $id_parent, 'data-icon' => $val['icon'], 'data-new' => $val['new']], 'text' => $val['nama_kategori']
            ];
            if (key_exists('children', $val)) {
                $result[$val['id']]['attr']['disabled'] = 'disabled';
                $this->buildKategoriList($val['children'], $val['id'], $result);
            }
        }

        return $result;
    }

    public function set_depth(&$result, $depth = 0)
    {
        foreach ($result as $key => &$val) {
            $val['depth'] = $depth;
            if (key_exists('children', $val)) {
                self::set_depth($val['children'], $val['depth'] + 1);
            }
        }
    }

    public function kategori_list($result)
    {
        // print_r($result);
        $refs = array();
        $list = array();

        foreach ($result as $key => $data) {
            if (!$key || empty($data['id'])) // Highlight OR No parent
                continue;

            $thisref = &$refs[$data['id']];
            foreach ($data as $field => $value) {
                $thisref[$field] = $value;
            }

            // no parent
            if ($data['parent_id'] == 0) {

                $list[$data['id']] = &$thisref;
            } else {

                $thisref['depth'] = ++$refs[$data['id']]['depth'];
                $refs[$data['parent_id']]['children'][$data['id']] = &$thisref;
            }
        }
        self::set_depth($list);
        return $list;
    }

    public function getKategori()
    {
        $result = [];

        $kategori = Kategori::orderby('urut')->get()->toArray();

        foreach ($kategori as $val) {
            $result[$val['id']] = $val;
            $result[$val['id']]['depth'] = 0;
        }

        return $result;
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
