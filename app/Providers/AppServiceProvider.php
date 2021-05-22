<?php

namespace App\Providers;

use App\Http\Controllers\Api\ShipmentController;
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
    {  
        app()->singleton('aramex', Aramex::class);
        app()->singleton('dhl', DHL::class);
        app()->singleton('fedex', Fedex::class);
        
        JsonResource::withoutWrapping();
    }
}
