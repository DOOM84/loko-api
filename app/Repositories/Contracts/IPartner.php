<?php


namespace App\Repositories\Contracts;


use Illuminate\Http\Request;

interface IPartner
{
    public function applyImage($name, $filename, $status);

}
