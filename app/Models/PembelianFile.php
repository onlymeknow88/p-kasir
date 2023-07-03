<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianFile extends Model
{
    use HasFactory;
    protected $table = 'pembelian_file';

    protected $fillable = ['id_pembelian', 'file_picker_id', 'urut'];
}
