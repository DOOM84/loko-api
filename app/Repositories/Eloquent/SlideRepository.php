<?php


namespace App\Repositories\Eloquent;


use App\Models\Slide;
use App\Repositories\Contracts\ISlide;
use Illuminate\Http\Request;

class SlideRepository extends BaseRepository implements ISlide
{

    public function model()
    {
        return Slide::class;
    }

}
