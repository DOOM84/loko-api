<?php


namespace App\Repositories\Contracts;


use Illuminate\Http\Request;

interface IFile
{
    public function applyFile($name, $filename, $status);

}
