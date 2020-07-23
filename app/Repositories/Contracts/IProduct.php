<?php


namespace App\Repositories\Contracts;


use Illuminate\Http\Request;

interface IProduct
{
    public function applyImage($id, $filename, $status);

}
