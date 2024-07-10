<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Http::macro('connector', function () {
            if( app()->isProduction() )
            {
                return Http::baseUrl(config('services.api.prod'))
                ->withBasicAuth('Bodega_Virtual', 'Sed.2024')
                ->withHeaders([
                    'x-api-key' =>  config('services.api.token_prod'),
                    'Content-Type' => 'application/json',
                ]);
            }
            else{
                return Http::baseUrl(config('services.api.dev'))
                ->withBasicAuth('Bodega_Virtual', 'Sed.2024')
                ->withHeaders([
                    'x-api-key' => config('services.api.token_dev'),
                    'Content-Type' => 'application/json',
                ]);
            }
        });


    }
}
