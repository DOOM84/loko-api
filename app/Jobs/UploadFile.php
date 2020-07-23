<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Storage;
use Image;
use File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $file;

    public function __construct(\App\Models\File $file)
    {
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $disk = 'public';
        $filename =  $this->file->path;
        $original_file = storage_path() . '/uploads/Files/original/' . $filename;

        try {
            //create the thumbnail  Img and save to tmp disk
            //Image::make($original_file)->save($thumbnail = storage_path('uploads/EditorImages/thumbnail/'. $filename));

            //store image to permanent disk
            //original image
            if(Storage::disk($disk)
                ->put('uploads/Files/original/'. $filename, fopen($original_file, 'r+'))){
                File::delete($original_file);
            }

            //thumbnail image
            /*if(Storage::disk($disk)
                ->put('uploads/EditorImages/thumbnail/'. $filename, fopen($thumbnail, 'r+'))){
                File::delete($thumbnail);
            }*/

        }catch (\Exception $e){
            \Log::error($e->getMessage());

        }
    }
}
