<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangImage extends Model
{
    use HasFactory;

    protected $table = 'barang_image';

    protected $fillable = ['barang_id', 'file_picker_id', 'urut'];
}
