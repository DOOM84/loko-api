<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use App\Http\Resources\DescriptionResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\InfoResource;
use App\Http\Resources\PartnerResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\SlideResource;
use App\Repositories\Contracts\IContact;
use App\Repositories\Contracts\IDescription;
use App\Repositories\Contracts\IFile;
use App\Repositories\Contracts\IInfo;
use App\Repositories\Contracts\IPartner;
use App\Repositories\Contracts\IPicture;
use App\Repositories\Contracts\IProduct;
use App\Repositories\Contracts\IService;
use App\Repositories\Contracts\ISlide;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    protected $info;
    protected $description;
    protected $service;
    protected $pictures;
    protected $dishes;
    protected $contacts;
    protected $files;
    protected $partners;
    protected $products;
    protected $slides;

    public function __construct(IInfo $info, IDescription $description, IFile $files, IService $service, IPicture $pictures,
                                IContact $contacts, IPartner $partners, IProduct $products, ISlide $slides)
    {
        $this->info = $info;
        $this->description = $description;
        $this->service = $service;
        $this->pictures = $pictures;
        $this->contacts = $contacts;
        $this->files = $files;
        $this->partners = $partners;
        $this->products = $products;
        $this->slides = $slides;


    }

    public function index()
    {
            $info = $this->info->findFirst();
            $description = $this->description->findFirst();
            $service = $this->service->findFirst();

        return response()->json([
            'info' => $info->status ? new InfoResource($info) : [],
            'description' => $description->status ? new DescriptionResource($description) : [],
            'service' => $service->status ? new ServiceResource($service) : [],
            'contact' => new ContactResource($this->contacts->findFirst()),
            'files' =>  FileResource::collection($this->files->findWhere('status', 1)),
            'partners' =>  PartnerResource::collection($this->partners->findWhere('status', 1)),
            'products' =>  ProductResource::collection($this->products->findWhere('status', 1)),
            'slides' =>  SlideResource::collection($this->slides->findWhere('status', 1)),
        ]);
    }

    public function contacts()
    {
        return response()->json([
            'contact' => new ContactResource($this->contacts->findFirst()),
        ]);
    }

    public function getProduct($slug)
    {
        return response()->json([
            'product' =>  new ProductResource($this->products->withCriteria([
                new EagerLoad(['details', 'pictures'])
            ])->findFirstBySeveralColumns(['slug' => $slug, 'status' => 1])),
        ]);

    }
}
