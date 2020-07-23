<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Picture extends Model
{
    protected $fillable = ['image', 'product_id', 'status'];

    public function product()
    {
        return $this->belongsTo(Product::class);

    }

    /*public function getImageAttribute()
    {
        return Storage::disk('public')
            ->url('uploads/Products/original/'.$this->image);
    }*/

   /* public function getImagesAttribute()
    {
        return [
            'thumbnail' => $this->getImagePath('thumbnail'),
            'original' => $this->getImagePath('original'),
        ];
    }

    protected function getImagePath($size)
    {
        return Storage::disk('public')
            ->url('uploads/Products/'.$size.'/'.$this->image);
    }*/
}
