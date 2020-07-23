<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\adminSlideResource;
use App\Jobs\UploadImage;
use App\Repositories\Contracts\ISlide;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class SlideController extends Controller
{
    protected $slides;

    public function __construct(ISlide $slides)
    {
        $this->slides = $slides;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        return response()->json([
            'slides' => adminSlideResource::collection($this->slides->all())
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
            'images' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5048'
        ],
            [
                'images.required' => 'Пожалуйста, укажите изображение слайда',
                'images.*.image' => 'Файл должен быть изображением',
                'images.*.mimes' => 'Изображение должно быть одним из форматов: jpeg,png,jpg,gif',
                'images.*.max' => 'Файл не должен превышать 5048 кб',
            ]);

        if($request->hasfile('images'))
        {
            foreach($request->file('images') as $image)
            {
                //$image_path = $image->getPathname();
                $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));
                $tmp = $image->storeAs('uploads/Images/original', $filename, 'tmp');
                $picture = $this->slides->create(['image' => $filename, 'status' => json_decode($request->status) ? true : false ]);
                $this->dispatch(new UploadImage($picture));
            }
        }

        return response()->json([
            'success' => 'Слайды успешно добавлены',
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
        $slide = $this->slides->update($id, [
            'status' => $request->status ? true : false,
        ]);

        return response()->json([
            'success' => 'Изображение успешно изменено',
            'slide' => new adminSlideResource($slide),
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
        $slide = $this->slides->find($id);

        foreach (['thumbnail', 'original'] as $size) {
            //check if file exists
            if (Storage::disk('public')->exists("/uploads/Images/{$size}/".$slide->image)){
                Storage::disk('public')->delete("/uploads/Images/{$size}/".$slide->image);
            }
        }

        $this->slides->delete($slide->id);

        return response()->json(['success' => 'Изображение успешно удалено'], Response::HTTP_ACCEPTED);
    }
}
