<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MaintenanceController extends Controller
{

    //validate cache active status
    public function isCache()
    {
        $cachestore = config('cache.default');
        if($cachestore){
            $cachestore = config("cache.stores.{$cachestore}");
        }
        if($cachestore)
        {
                return true;
        }
        return  false;
    }

    public function clearCache()
    {
        if($this->isCache()){
            Artisan::call('config:clear');

            Artisan::call('cache:clear');

            Artisan::call('view:clear');

            Artisan::call('route:clear');

            Artisan::call('config:cache');

            Log::info("Cache cleared!!!");

            return response()->json([
                'status' => 'success',
                'message' => 'Application cache cleared successfully.'
            ], 200);
        }
        Log::info("Cache inactive!!!");
        return response()->json([
            'status' => 'cache is not active',
            'message' => 'Application cache not found.'
        ], 404);
    }
}
