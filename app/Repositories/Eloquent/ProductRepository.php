<?php


namespace App\Repositories\Eloquent;


use App\Models\Product;
use App\Repositories\Contracts\IProduct;
use Illuminate\Http\Request;

class ProductRepository extends BaseRepository implements IProduct
{

    public function model()
    {
        return Product::class;
    }

    public function applyImage($id, $filename, $status)
    {
        $product = $this->model->find($id);
        $image = $product->pictures()->create([
            'image' => $filename,
            'status' => $status,
        ]);
        return $image;
    }

}
