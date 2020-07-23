<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\adminPartnerResource;
use App\Jobs\UploadPartner;
use App\Repositories\Contracts\IPartner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PartnerController extends Controller
{
    protected $partners;

    public function __construct(IPartner $partners)
    {
        $this->partners = $partners;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        return response()->json([
            'partners' => adminPartnerResource::collection($this->partners->all())
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ],
            [
                'image.required' => 'Пожалуйста, укажите изображение партнера',
                'image.image' => 'Файл должен быть изображением',
                'image.mimes' => 'Изображение должно быть одним из форматов: jpeg,png,jpg,gif',
                'image.max' => 'Файл не должен превышать 2048 кб',
            ]);

        if($request->hasfile('image'))
        {
                $file = $request->file('image');
                $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($file->getClientOriginalName()));
                $tmp = $file->storeAs('uploads/Partners/original', $filename, 'tmp');
                $uploadedFile = $this->partners->applyImage($request->name ? $request->name : '',
                    $filename, json_decode($request->status) ? true : false);
                $this->dispatch(new UploadPartner($uploadedFile));
        }

        return response()->json([
            'success' => 'Партнер успешно добавлен',
            'partner' => new adminPartnerResource($uploadedFile),
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
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ],
            [
                'image.image' => 'Файл должен быть изображением',
                'image.mimes' => 'Изображение должно быть одним из форматов: jpeg,png,jpg,gif',
                'image.max' => 'Файл не должен превышать 2048 кб',
            ]);

        $filename = '';
        $partner = $this->partners->find($id);
        if($request->hasfile('image'))
        {
            foreach (['thumbnail', 'original'] as $size) {
                //check if file exists
                if (Storage::disk('public')->exists("/uploads/Partners/{$size}/".$partner->image)){
                    Storage::disk('public')->delete("/uploads/Partners/{$size}/".$partner->image);
                }
            }

            $uploadedFile = $request->file('image');
            $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($uploadedFile->getClientOriginalName()));
            $tmp = $uploadedFile->storeAs('uploads/Partners/original', $filename, 'tmp');
        }

        $res = $this->partners->update($id, [
            'name' => $request->name ? $request->name : '',
            'status' => json_decode($request->status) ? true : false,
            'image' => $filename ? $filename : $partner->image,
        ]);

        if($request->hasfile('image')){
            $this->dispatch(new UploadPartner($res));
        }

        return response()->json([
            'success' => 'Партнер успешно изменен',
            'partner' => new adminPartnerResource($res),
        ], Response::HTTP_ACCEPTED);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */


    public function destroy($id)
    {
        $partner = $this->partners->find($id);

        foreach (['thumbnail', 'original'] as $size) {
            //check if file exists
            if (Storage::disk('public')->exists("/uploads/Partners/{$size}/".$partner->image)){
                Storage::disk('public')->delete("/uploads/Partners/{$size}/".$partner->image);
            }
        }

        $this->partners->delete($partner->id);

        return response()->json(['success' => 'Партнер успешно удален'], Response::HTTP_ACCEPTED);
    }
}
