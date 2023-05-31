<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuStatus extends Model
{
    use HasFactory;

    protected $table = 'menu_status';

    protected $fillable = ['nama_status','keterangan'];
}
