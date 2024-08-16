<?php

namespace App\Http\Controllers;

use App\Models\Affinity;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Mockery\Undefined;

class AffinityController extends Controller
{

    //validate cache active status
    public function index()
    {
        $affinities = Affinity::all();
        $brands = app(CategoryController::class)->childGroups('marca',0);
        //Log::info($affinities);
        //Log::info($brands);
        return view('product.affinity',['affinities'=>$affinities,'brands'=>$brands]);
    }


    /**
     * @param string $brand. With a code, it retrieves the brand. IF = all THEN RETRIEVES ALL if = active THEN RETRIEVES ALL is_active = 1
     */
    public function getAffinities(Request $request, string $brand){
        if(!$brand ){
            return response()->json([
                'message' => 'invalid affinity data',
                'code' => 500,
            ], 500);
        }
        switch ($brand) {
            case 'all':
                $affinities = Affinity::all();
                break;
            case 'active':
                $affinities = Affinity::where( 'is_active', '1')->get();
                break;
            default:
                $affinities = Affinity::where( 'brand', "{$brand}")->get();
                break;
        }

        if (count($affinities) == 0) {
            return response()->json([
                'message' => "Error Affinities not found",
                'code' => 404,
            ], 404);
        }
        return response()->json($affinities->toArray(), 200);

    }
    public function createOrUpdateAffinity(Request $request, string $brand){
        $userLogged =  $request->input('sender_email');
        if (app(ProfileController::class)->hasAbility($userLogged, 'user-edit')) {
            $name =  $request->input('name');
            $url =  $request->input('url');
            $is_active =  $request->input('is_active')?? 1;
            Log::info("affinity createOrUpdateAffinity ", ['name' => $name, 'url' => $url, 'is_active' => $is_active]);
            if(!$brand || !$name || !$url ){
                return response()->json([
                    'message' => 'invalid affinity data',
                    'code' => 500,
                ], 500);
            }
            $brands = app(CategoryController::class)->brands($brand);
            Log::info($brands);
            if (count($brands) == 0) {
                return response()->json([
                    'message' => "Error Brand not found {$brand}",
                    'code' => 404,
                ], 404);
            }

            $message = "Affinity ";
            $affinity = Affinity::where( 'brand', "{$brand}")->first();

            if (!$affinity){
                $affinity = Affinity::create([
                    'brand' => $brands[0]['name'],
                    'name' => $name,
                    'url' => $url,
                    'is_active' => $is_active,
                ]);
                $message = $message .$affinity['name'] ." created.";
            } else {
                $affinity['name'] = $name;
                $affinity['url'] = $url;
                $affinity['is_active'] = $is_active;
                $affinity->save();
                $message = $message .$affinity['name'] ." updated.";
            }
            return response()->json([
                'message' => $message,
                'code' => 200,
            ], 200);
        }
        return response()->json([
            'message' => 'Unauthorized - Access is denied due to invalid credentials.',
            'code' => 401,
        ], 401);
    }


}
