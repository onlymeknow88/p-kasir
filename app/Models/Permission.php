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
}
