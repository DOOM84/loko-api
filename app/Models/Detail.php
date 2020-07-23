<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $fillable = ['name', 'drawing', 'weight', 'status'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'detail_product');
    }
}
