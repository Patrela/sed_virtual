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
        Http::macro('sedfunc', function () {
            return Http::baseUrl(config('services.api.erp'))
                ->withBasicAuth('Bodega_Virtual', 'Sed.2024')
                ->withHeaders([
                    'x-api-key' => 'HsMaBkQPI1ReVNN1ppIZ9KXTeSTSJNJ9VNMY4U2bSxi2y',
                    'Content-Type' => 'application/json',
                ]);
        });
    }
}
