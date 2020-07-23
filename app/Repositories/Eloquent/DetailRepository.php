<?php


namespace App\Repositories\Eloquent;


use App\Models\Detail;
use App\Repositories\Contracts\IDetail;
use Illuminate\Http\Request;

class DetailRepository extends BaseRepository implements IDetail
{

    public function model()
    {
        return Detail::class;
    }

}
