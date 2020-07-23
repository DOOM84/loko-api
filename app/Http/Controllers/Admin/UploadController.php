<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\UploadEditorImage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function uploadEditorImage(Request $request)
    {
        if($request->hasfile('image'))
        {
            $image = $request->file('image');
            $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));
            $tmp = $image->storeAs('uploads/EditorImages/original', $filename, 'tmp');
        }
        $this->dispatch(new UploadEditorImage($filename));

        return response()->json([
            'path' =>  Storage::disk('public')
                ->url('uploads/EditorImages/original/'.$filename)
        ], 200);
    }
}
