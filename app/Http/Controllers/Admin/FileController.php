<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\adminFileResource;
use App\Jobs\UploadFile;
use App\Repositories\Contracts\IFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    protected $files;

    public function __construct(IFile $files)
    {
        $this->files = $files;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        return response()->json([
            'files' => adminFileResource::collection($this->files->all())
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
            'name' => 'required',
            'file' => 'required|file|max:5048'
        ],
            [
                'name.required' => 'Пожалуйста, укажите название файла',
                'file.required' => 'Пожалуйста, укажите файл',
                'file.file' => 'Файл должен быть файлом',
                'file.max' => 'Файл не должен превышать 5048 кб',
            ]);

        if($request->hasfile('file'))
        {
                $file = $request->file('file');
                $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($file->getClientOriginalName()));
                $tmp = $file->storeAs('uploads/Files/original', $filename, 'tmp');
                $uploadedFile = $this->files->applyFile($request->name, $filename, json_decode($request->status) ? true : false);
                $this->dispatch(new UploadFile($uploadedFile));
        }

        return response()->json([
            'success' => 'Файл(ы) успешно добавлены',
            'file' => new adminFileResource($uploadedFile),
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
            'file' => 'sometimes|file|max:5048',
            'name' => 'required',
        ],
            [
                'name.required' => 'Пожалуйста, укажите название файла',
                'file.file' => 'Файл должен быть файлом',
                'file.max' => 'Файл не должен превышать 5048 кб',
            ]);

        $filename = '';
        $file = $this->files->find($id);
        if($request->hasfile('file'))
        {
            if (Storage::disk('public')->exists("/uploads/Files/original/".$file->path)){
                Storage::disk('public')->delete("/uploads/Files/original/".$file->path);
            }

            $uploadedFile = $request->file('file');
            $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($uploadedFile->getClientOriginalName()));
            $tmp = $uploadedFile->storeAs('uploads/Files/original', $filename, 'tmp');
        }

        $res = $this->files->update($id, [
            'name' => $request->name,
            'status' => json_decode($request->status) ? true : false,
            'path' => $filename ? $filename : $file->path,
        ]);

        if($request->hasfile('file')){
            $this->dispatch(new UploadFile($res));
        }

        return response()->json([
            'success' => 'Файл успешно изменен',
            'file' => new adminFileResource($res),
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
        $file = $this->files->find($id);

        if (Storage::disk('public')->exists("/uploads/Files/original/".$file->path)){
            Storage::disk('public')->delete("/uploads/Files/original/".$file->path);
        }

        $this->files->delete($file->id);

        return response()->json(['success' => 'Файл успешно удален'], Response::HTTP_ACCEPTED);
    }
}
