<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\adminDetailResource;
use App\Http\Resources\adminProductResource;
use App\Jobs\UploadDetail;
use App\Repositories\Contracts\IDetail;
use App\Repositories\Contracts\IProduct;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DetailController extends Controller
{
    protected $details, $products;

    public function __construct(IDetail $details, IProduct $products)
    {
        $this->details = $details;
        $this->products = $products;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'details' => adminDetailResource::collection($this->details
                ->withCriteria([
                    new EagerLoad(['products'])
                ])
            ->all()),
            'products' => adminProductResource::collection($this->products->all()),
        ]);

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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'drawing' => 'required',
            'weight' => 'required',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ],
            [
                'name.required' => 'Пожалуйста, укажите наименование',
                'drawing.required' => 'Пожалуйста, укажите номер чертежа',
                'weight.required' => 'Пожалуйста, укажите вес (кг)',
                'image.image' => 'Файл должен быть изображением',
                'image.mimes' => 'Изображение должно быть одним из форматов: jpeg,png,jpg,gif',
                'image.max' => 'Файл не должен превышать 2048 кб',
            ]);

        $request['status'] = json_decode($request->status) ? true : false;

        $detail = $this->details->create($request->except('ids', 'image'));

        if($request->hasfile('image'))
        {
            $file = $request->file('image');
            $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($file->getClientOriginalName()));
            $tmp = $file->storeAs('uploads/Details/original', $filename, 'tmp');
            /*$uploadedFile = $this->details->applyImage($request->name ? $request->name : '',
                $filename, json_decode($request->status) ? true : false);*/
            $detail->image = $filename;
            $detail->save();
            $this->dispatch(new UploadDetail($detail));
        }

        $this->details->syncRelation($detail->id, 'products', $request->ids);

        return response(['success' => 'Деталь успешно добавлена',
            'detail' => new adminDetailResource($detail),
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required',
            'drawing' => 'required',
            'weight' => 'required',
        ],
            [
                'name.required' => 'Пожалуйста, укажите наименование',
                'drawing.required' => 'Пожалуйста, укажите номер чертежа',
                'weight.required' => 'Пожалуйста, укажите вес (кг)',
                'image.image' => 'Файл должен быть изображением',
                'image.mimes' => 'Изображение должно быть одним из форматов: jpeg,png,jpg,gif',
                'image.max' => 'Файл не должен превышать 2048 кб',
            ]);

            $request['status'] = json_decode($request->status) ? true : false;

            $this->details->syncRelation($id, 'products', $request->ids);
            $detail = $this->details->update($id, $request->except('ids', 'image'));

        if($request->hasfile('image'))
        {
            $file = $request->file('image');
            $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($file->getClientOriginalName()));
            $tmp = $file->storeAs('uploads/Details/original', $filename, 'tmp');

            if (Storage::disk('public')->exists("/uploads/Details/original/".$detail->image)){
                Storage::disk('public')->delete("/uploads/Details/original/".$detail->image);
            }

            $detail->image = $filename;
            $detail->save();
            $this->dispatch(new UploadDetail($detail));
        }

        return response(['success' => 'Деталь успешно изменена',
            'detail' => new adminDetailResource($detail),
            ], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //$user = $this->users->find($id);

        $this->details->detachRelation($id, 'products');

        $detail = $this->details->find($id);

        if (Storage::disk('public')->exists("/uploads/Details/original/".$detail->image)){
            Storage::disk('public')->delete("/uploads/Details/original/".$detail->image);
        }

        $this->details->delete($id);

        return response(['success' => 'Деталь успешно удалена'], Response::HTTP_ACCEPTED);
    }
}
