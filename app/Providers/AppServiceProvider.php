<?php

namespace App\Providers;

use App\Services\DHL;
use App\Services\Aramex;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\ShippingAdapterInterface;
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
        $this->app->singleton( ShippingAdapterInterface::class, Aramex::class);
        $this->app->singleton( ShippingAdapterInterface::class, DHL::class);
        JsonResource::withoutWrapping();
    }
}
