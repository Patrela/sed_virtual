<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CacheController;

class SwaggerController extends Controller
{
    public function show()
    {
        if (app(CacheController::class)->hasAbility(Auth::user()->email, 'document-read')) {
            //Log::info("document-read ability inside collection");

            $swaggerJsonUrl = url('api/documentation/json/swagger.json');
            return view('documentation.swagger-ui', ['swaggerJsonUrl' => $swaggerJsonUrl]);
        }
        return response()->json([
            'message' => 'Unauthorized - Access is denied due to invalid credentials.',
            'code' => 401,
        ], 401);
    }

    public function getSwaggerJson()
    {
        if (app(CacheController::class)->hasAbility(Auth::user()->email, 'document-read')) {
            $yamlFile = Storage::disk('public')->get('openapi.yaml');
            //dd($yamlFile);
            //Log::info($yamlFile);

            $openApiSpec = Yaml::parse($yamlFile);
            return response()->json($openApiSpec);
        }
        return response()->json([
            'message' => 'Unauthorized - Access is denied due to invalid credentials.',
            'code' => 401,
        ], 401);
    }
}
