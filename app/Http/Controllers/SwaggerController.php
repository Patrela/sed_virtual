<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ProfileController;

class SwaggerController extends Controller
{
    public function show()
    {
        if (app(ProfileController::class)->hasAbility(Auth::user()->email, 'document-read')) {
            //Log::info("document-read ability inside collection");

            $swaggerJsonUrl = route('documentation.json'); // url('documentation/json/swagger.json');
            return view('documentation.swagger-ui', ['swaggerJsonUrl' => $swaggerJsonUrl]);
        }
        return response()->json([
            'message' => 'Unauthorized - Access is denied due to invalid credentials.',
            'code' => 401,
        ], 401);
    }

    public function getSwaggerJson()
    {
        if (app(ProfileController::class)->hasAbility(Auth::user()->email, 'document-read')) {
            $yamlFile = Storage::disk('public')->get('openapitransactions.yaml'); //openapistock.yaml
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

    public function getSwaggerJsonSource( int $source)
    {
        if (app(ProfileController::class)->hasAbility(Auth::user()->email, 'document-read')) {
            switch ($source) {
                case 1: $yaml = "openapistock.yaml"; break;
                case 2: $yaml = "openapitransactions.yaml"; break;
            }
            $yamlFile = Storage::disk('public')->get("{$yaml}"); //openapistock.yaml
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

    public function getSwaggerDocumentationSource( int $source)
    {
        if (app(ProfileController::class)->hasAbility(Auth::user()->email, 'document-read')) {
            //Log::info("document-read ability inside collection");

            $swaggerJsonUrl = route('documentation.source',['source'=> $source]); // url('documentation/json/swagger.json');
            return view('documentation.swagger-ui', ['swaggerJsonUrl' => $swaggerJsonUrl]);
        }
        return response()->json([
            'message' => 'Unauthorized - Access is denied due to invalid credentials.',
            'code' => 401,
        ], 401);
    }
}
