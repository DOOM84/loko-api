<?php


namespace App\Repositories\Eloquent;


use App\Models\File;
use App\Repositories\Contracts\IFile;
use Illuminate\Http\Request;

class FileRepository extends BaseRepository implements IFile
{

    public function model()
    {
        return File::class;
    }

    public function applyFile($name, $filename, $status)
    {
        $file = $this->model->create([
            'name' => $name,
            'path' => $filename,
            'status' => $status,
        ]);
        return $file;
    }

}
