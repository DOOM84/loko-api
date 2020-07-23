<?php


namespace App\Repositories\Eloquent;


use App\Models\Partner;
use App\Repositories\Contracts\IPartner;
use Illuminate\Http\Request;

class PartnerRepository extends BaseRepository implements IPartner
{

    public function model()
    {
        return Partner::class;
    }

    public function applyImage($name, $filename, $status)
    {
        $file = $this->model->create([
            'name' => $name,
            'image' => $filename,
            'status' => $status,
        ]);
        return $file;
    }

}
