<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'role';

    protected $fillable = ['nama_role','keterangan','judul_role','menu_id'];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
