<?php

namespace App\Providers;

use App\Http\Controllers\Api\ShippingController;
use App\Services\DHL;
use App\Services\Aramex;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\ShippingAdapterInterface;
use App\Services\Fedex;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {     // bind users role with the Ads Query corresponding to it
        //     app()->singleton('guest', \App\Ads\Domain\IndexAdsQueries\GuestIndexAdsQuery::class);
        //     app()->singleton('admin', \App\Ads\Domain\IndexAdsQueries\AdminIndexAdsQuery::class);
        //     app()->singleton('advertiser', \App\Ads\Domain\IndexAdsQueries\AdvertiserIndexAdsQuery::class);
        //     app()->singleton('soldier', \App\Ads\Domain\IndexAdsQueries\SoldierIndexAdsQuery::class);

        //     // Contextual Binding of the (Index Ads Query) Factory with Ads Query classes

        app()->singleton('aramex', Aramex::class);
        app()->singleton('dhl', DHL::class);
        app()->singleton('fedex', Fedex::class);

        // app()->when(\App\Http\Controllers\Api\ShippingController::class)
        //     ->needs(\App\Interfaces\ShippingAdapterInterface::class)
        //     ->give(function ($shippingProvider) {
        //         dd(request());
        //     });

        // app()->when(Shipment)

        JsonResource::withoutWrapping();
    }
}
