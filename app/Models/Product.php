<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use Sluggable;

    protected $fillable = ['name', 'slug', 'image', 'status'];

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function details()
    {
        return $this->belongsToMany(Detail::class, 'detail_product');
    }

    public function pictures()
    {
        return $this->hasMany(Picture::class);
    }

    public function getImagesAttribute()
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
    }


}
