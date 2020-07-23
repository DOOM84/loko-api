<?php
namespace App\Repositories\Eloquent;

use App\Models\Description;
use App\Repositories\Contracts\IDescription;

class DescriptionRepository extends BaseRepository implements IDescription
{
    public function model()
    {
        return Description::class;
    }


}
