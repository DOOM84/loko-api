<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\adminDescriptionResource;
use App\Repositories\Contracts\IDescription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class DescriptionController extends Controller
{
    protected $descriptions;

    public function __construct(IDescription $descriptions)
    {
        $this->descriptions = $descriptions;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'description' =>  new adminDescriptionResource($this->descriptions->findFirst())
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
        $info = $this->descriptions->create([
            'text_content' => $request->text_content,
            'status' => $request->status ? true : false
        ]);

        return response()->json([
            'success' => 'Информация успешно создана',
            'description' => new adminDescriptionResource($info),
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
        $description = $this->descriptions->update($id, [
            'text_content' => $request->text_content,
            'status' => $request->status ? true : false
        ]);

        return response()->json([
            'success' => 'Информация успешно изменена',
            'description' => new adminDescriptionResource($description),
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
