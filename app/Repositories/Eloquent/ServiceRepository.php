<?php
namespace App\Repositories\Eloquent;

use App\Models\Service;
use App\Repositories\Contracts\IService;

class ServiceRepository extends BaseRepository implements IService
{
    public function model()
    {
        return Service::class;
    }


}
