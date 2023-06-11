<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permission';

    protected $fillable = [
        'role_id', 'menu_id', 'akses', 'tambah', 'edit', 'view', 'hapus'
    ];

    public function menus()
    {
        return $this->belongsTo(Menu::class,'menu_id');
    }

    public function roles()
    {
        return $this->belongsTo(Role::class,'role_id');
    }
}
