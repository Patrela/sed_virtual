<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\SedController;

class ProductsSed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:products-sed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import and Update SED Products inventory items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sync = Cache::remember('sync_products_last_run', now()->addMinutes(30), function () {
            $sedController = new SedController();
            $response = $sedController->syncProductsAPI();
            return $response;
        });
    }
}
