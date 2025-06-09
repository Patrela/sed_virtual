<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageValidationRepository
{
    public function validateImageUrl(string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        try {
            $response = Http::withoutVerifying()->timeout(1)->head($url);
            return $response->ok();
        } catch (\Exception $e) {
            //Log::error("Error validando URL: {$url} - " . $e->getMessage());
            return false;
        }
    }

    
}
