<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\adminServiceResource;
use App\Repositories\Contracts\IService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends Controller
{
    protected $services;

    public function __construct(IService $services)
    {
        $this->services = $services;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'service' =>  new adminServiceResource($this->services->findFirst())
        ], 200);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $service = $this->services->create([
            'text_content' => $request->text_content,
            'status' => $request->status ? true : false
        ]);

        return response()->json([
            'success' => 'Информация успешно создана',
            'service' => new adminServiceResource($service),
        ], Response::HTTP_ACCEPTED);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $service = $this->services->update($id, [
            'text_content' => $request->text_content,
            'status' => $request->status ? true : false
        ]);

        return response()->json([
            'success' => 'Информация успешно изменена',
            'service' => new adminServiceResource($service),
        ], Response::HTTP_ACCEPTED);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */


    public function destroy($id)
    {
        //
    }
}
