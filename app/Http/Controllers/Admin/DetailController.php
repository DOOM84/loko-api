<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\adminDetailResource;
use App\Http\Resources\adminProductResource;
use App\Repositories\Contracts\IDetail;
use App\Repositories\Contracts\IProduct;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class DetailController extends Controller
{
    protected $details, $products;

    public function __construct(IDetail $details, IProduct $products)
    {
        $this->details = $details;
        $this->products = $products;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'details' => adminDetailResource::collection($this->details
                ->withCriteria([
                    new EagerLoad(['products'])
                ])
            ->all()),
            'products' => adminProductResource::collection($this->products->all()),
        ]);

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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'drawing' => 'required',
            'weight' => 'required',
        ],
            [
                'name.required' => 'Пожалуйста, укажите наименование',
                'drawing.required' => 'Пожалуйста, укажите номер чертежа',
                'weight.required' => 'Пожалуйста, укажите вес (кг)',
            ]);

        $request['status'] = !$request['status'] ? 0 : 1;

        $detail = $this->details->create($request->except('ids'));

        $this->details->syncRelation($detail->id, 'products', $request->ids);

        return response(['success' => 'Деталь успешно добавлена',
            'detail' => new adminDetailResource($detail),
        ], Response::HTTP_CREATED);
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'drawing' => 'required',
            'weight' => 'required',
        ],
            [
                'name.required' => 'Пожалуйста, укажите наименование',
                'drawing.required' => 'Пожалуйста, укажите номер чертежа',
                'weight.required' => 'Пожалуйста, укажите вес (кг)',
            ]);


            $this->details->syncRelation($id, 'products', $request->ids);

        $detail = $this->details->update($id, $request->except('ids'));

        return response(['success' => 'Деталь успешно изменена',
            'detail' => new adminDetailResource($detail),
            ], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //$user = $this->users->find($id);

        $this->details->detachRelation($id, 'products');

        $this->details->delete($id);

        return response(['success' => 'Деталь успешно удалена'], Response::HTTP_ACCEPTED);
    }
}
