<?php

namespace App\Jobs;

use App\Models\Detail;
use Illuminate\Support\Facades\Storage;
use Image;
use File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadDetail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $picture;

    public function __construct(Detail $picture)
    {
        $this->picture = $picture;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $disk = 'public'; //$this->design->disk;
        $filename =  $this->picture->image;
        $original_file = storage_path() . '/uploads/Details/original/' . $filename;

        try {
            //create the large Img and save to tmp disk
            Image::make($original_file)/*->fit(800, 600, function ($constraint){
                $constraint->aspectRatio();
            })*/->save($large = storage_path('uploads/Details/original/'. $filename));


            //create the thumbnail  Img and save to tmp disk
            //Image::make($original_file)->save($thumbnail = storage_path('uploads/EditorImages/thumbnail/'. $filename));

            //store image to permanent disk
            //original image
            if(Storage::disk($disk)
                ->put('uploads/Details/original/'. $filename, fopen($original_file, 'r+'))){
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
