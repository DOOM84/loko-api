<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $fillable = ['name', 'path', 'status'];

    protected function getFileAttribute()
    {
        return Storage::disk('public')
            ->url('uploads/Files/original/'.$this->path);
    }
}
