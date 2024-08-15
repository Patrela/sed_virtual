<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;


class CacheController extends Controller
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
    public function hasAbility($email, $ability)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return false; // Or throw an exception
        }

        $abilities = $user->tokens->pluck('abilities')->flatten();
        return $abilities->contains($ability); // 'document-read'
    }

}
