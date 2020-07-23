<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\{IContact,
    IDescription,
    IDetail,
    IFile,
    IInfo,
    IPartner,
    IPicture,
    IProduct,
    IService,
    ISlide,
    IUser};
use App\Repositories\Eloquent\{ContactRepository,
    DescriptionRepository,
    DetailRepository,
    FileRepository,
    InfoRepository,
    PartnerRepository,
    PictureRepository,
    ProductRepository,
    ServiceRepository,
    SlideRepository,
    UserRepository};

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(IPicture::class, PictureRepository::class);
        $this->app->bind(IDetail::class, DetailRepository::class);
        $this->app->bind(IFile::class, FileRepository::class);
        $this->app->bind(IPartner::class, PartnerRepository::class);
        $this->app->bind(IProduct::class, ProductRepository::class);
        $this->app->bind(IInfo::class, InfoRepository::class);
        $this->app->bind(IService::class, ServiceRepository::class);
        $this->app->bind(IDescription::class, DescriptionRepository::class);
        $this->app->bind(IContact::class, ContactRepository::class);
        $this->app->bind(ISlide::class, SlideRepository::class);
        $this->app->bind(IUser::class, UserRepository::class);

    }
}
