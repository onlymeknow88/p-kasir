<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingApp extends Model
{
    use HasFactory;

    protected $table = 'setting';

    protected $fillable = ['type', 'param', 'value'];

}
