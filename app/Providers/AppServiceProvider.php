<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

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
        Http::macro('seddev', function () {
            return Http::baseUrl(config('services.api.dev'))
                ->withBasicAuth('Bodega_Virtual', 'Sed.2024')
                ->withHeaders([
                    'x-api-key' => 'HsMaBkQPI1ReVNN1ppIZ9KXTeSTSJNJ9VNMY4U2bSxi2y',
                    'Content-Type' => 'application/json',
                ]);
        });

        Http::macro('sedprod', function () {
            return Http::baseUrl(config('services.api.prod'))
                ->withBasicAuth('Bodega_Virtual', 'Sed.2024')
                ->withHeaders([
                    'x-api-key' => 'TfBS4ZNFr9JxUqKQjiGmTanp29Ocix8TJORDCnTo4wg8q',
                    'Content-Type' => 'application/json',
                ]);
        });
    }
}
