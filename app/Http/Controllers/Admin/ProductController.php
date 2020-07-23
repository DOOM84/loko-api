<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\adminProductResource;
use App\Jobs\UploadMoreImages;
use App\Jobs\UploadProduct;
use App\Repositories\Contracts\IPicture;
use App\Repositories\Contracts\IProduct;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    protected $products, $pictures;

    public function __construct(IProduct $products, IPicture $pictures)
    {
        $this->products = $products;
        $this->pictures = $pictures;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        return response()->json([
            'products' => adminProductResource::collection($this->products
                ->withCriteria([
                    new EagerLoad(['details', 'pictures'])
                ])
                ->all()),
            //'products' => adminProductResource::collection($this->products->all()),
        ], 200);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5048',
            'images.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5048',
            'name' => 'required|string',
        ],
            [
                'image.required' => 'Пожалуйста, укажите изображение продукции',
                'image.image' => 'Файл должен быть изображением',
                'image.max' => 'Файл не должен превышать 5048 кб',
                'image.mimes' => 'Изображение должно быть одним из форматов: jpeg,png,jpg,gif',
                'images.*.image' => 'Файл должен быть изображением',
                'images.*.mimes' => 'Изображение должно быть одним из форматов: jpeg,png,jpg,gif',
                'images.*.max' => 'Файл не должен превышать 5048 кб',
            ]);

        if($request->hasfile('image'))
        {
                $file = $request->file('image');
                $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($file->getClientOriginalName()));
                $tmp = $file->storeAs('uploads/Products/original', $filename, 'tmp');
                $uploadedFile = $this->products->create(
                    [
                        'name'=> $request->name,
                        'image'=> $filename,
                        'status'=> json_decode($request->status) ? true : false

                    ]);
                $this->dispatch(new UploadProduct($uploadedFile));
        }

        if($request->hasfile('images'))
        {
            foreach($request->file('images') as $image)
            {
                //$image_path = $image->getPathname();
                $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));
                $tmp = $image->storeAs('uploads/Products/original', $filename, 'tmp');
                $picture = $this->products->applyImage($uploadedFile->id, $filename, true);
                $this->dispatch(new UploadMoreImages($picture));
            }
        }

        return response()->json([
            'success' => 'Товар успешно добавлен',
            'product' => new adminProductResource($uploadedFile),
        ], Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5048',
            'images.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5048',
            'name' => 'required|string',
        ],
        [
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Изображение должно быть одним из форматов: jpeg,png,jpg,gif',
            'image.max' => 'Файл не должен превышать 5048 кб',
            'images.*.image' => 'Файл должен быть изображением',
            'images.*.mimes' => 'Изображение должно быть одним из форматов: jpeg,png,jpg,gif',
            'images.*.max' => 'Файл не должен превышать 5048 кб',
        ]);

        $filename = '';
        $product = $this->products->find($id);
        if($request->hasfile('image'))
        {
            foreach (['thumbnail', 'original'] as $size) {
                //check if file exists
                if (Storage::disk('public')->exists("/uploads/Products/{$size}/".$product->image)){
                    Storage::disk('public')->delete("/uploads/Products/{$size}/".$product->image);
                }
            }

            $uploadedFile = $request->file('image');
            $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($uploadedFile->getClientOriginalName()));
            $tmp = $uploadedFile->storeAs('uploads/Products/original', $filename, 'tmp');
        }

        $res = $this->products->update($id, [
            'name' => $request->name,
            'status' => json_decode($request->status) ? true : false,
            'image' => $filename ? $filename : $product->image,
        ]);

        if($request->hasfile('image')){
            $this->dispatch(new UploadProduct($res));
        }

        if($request->hasfile('images'))
        {
            foreach($request->file('images') as $image)
            {
                $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));
                $tmp = $image->storeAs('uploads/Products/original', $filename, 'tmp');
                $picture = $this->products->applyImage($id, $filename, true);
                $this->dispatch(new UploadMoreImages($picture));
            }
        }

        return response()->json([
            'success' => 'Товар успешно изменен',
            'product' => new adminProductResource($res->refresh()),
        ], Response::HTTP_ACCEPTED);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */


    public function delProdPic(Request $request)
    {
        $picture = $this->pictures->find($request->picId);

        foreach (['thumbnail', 'original'] as $size) {
            //check if file exists
            if (Storage::disk('public')->exists("/uploads/Products/{$size}/".$picture->image)){
                Storage::disk('public')->delete("/uploads/Products/{$size}/".$picture->image);
            }
        }

        $this->pictures->delete($request->picId);

        return response()->json(['success' => 'Изображение успешно удалено'], Response::HTTP_ACCEPTED);
    }

    public function updProdPic(Request $request)
    {
        $res = $this->pictures->update($request->id, [
            'status' => json_decode($request->status) ? true : false,
        ]);

        return response()->json(['success' => 'Изображение успешно изменено'], Response::HTTP_ACCEPTED);
    }


    public function delProdDetRelation(Request $request)
    {
        $this->products->detachRelation($request->prId, 'details', $request->detId);
        return response()->json(['success' => 'Связь удалена успешно'], Response::HTTP_ACCEPTED);
    }


    public function destroy($id)
    {
        $this->products->detachRelation($id, 'details');

        $product = $this->products->find($id);

        foreach (['thumbnail', 'original'] as $size) {
            //check if file exists
            if (Storage::disk('public')->exists("/uploads/Products/{$size}/".$product->image)){
                Storage::disk('public')->delete("/uploads/Products/{$size}/".$product->image);
            }
        }

        foreach ($product->pictures as $picture) {
            foreach (['thumbnail', 'original'] as $size) {
                //check if file exists
                if (Storage::disk('public')->exists("/uploads/Products/{$size}/".$picture->image)){
                    Storage::disk('public')->delete("/uploads/Products/{$size}/".$picture->image);
                }
            }
        }

        $this->products->deleteRelation($id, 'pictures');

        $this->products->delete($product->id);

        return response()->json(['success' => 'Товар успешно удален'], Response::HTTP_ACCEPTED);
    }
}
