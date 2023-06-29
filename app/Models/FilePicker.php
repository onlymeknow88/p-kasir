<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilePicker extends Model
{
    use HasFactory;

    protected $table = 'file_picker';

    protected $fillable = ['title', 'caption', 'description', 'alt_text', 'nama_file', 'mime_type', 'size', 'tgl_upload', 'user_id_upload', 'meta_file'];
}
