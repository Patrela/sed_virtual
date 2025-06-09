<?php

namespace App\Http\Controllers;


use App\Models\Trade;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class TradeController extends Controller
{
    public function index()
    {
        $trades = Trade::all()
                ->orderBy('name', 'ASC')
        ;
        return response()->json($trades->toArray(), 200);
    }
    public function show(string $trade)
    {
        if (!$trade) {
            return response()->json([
                'message' => 'invalid trade data',
                'code' => 500,
            ], 500);
        }
        switch ($trade) {
            case 'all':
                $trades = Trade::all();
                break;
            // case 'active':
            //     $trades = Trade::where( 'is_program_active', '1')->get();
            //     break;
            default:
                $trades = Trade::where('trade_id', "{$trade}")->get();
                break;
        }

        if (count($trades) == 0) {
            return response()->json([
                'message' => "Error Trade not found",
                'code' => 404,
            ], 404);
        }
        return response()->json($trades->toArray(), 200);
    }

    public function getTradeByEmail(string $email, string $trade_token) 
    {
        if (!$email || !$trade_token) {
            return null;
        }
        //log::info("getTradeByEmail: {$email} {$trade_token} PRODUCTION: " . app()->isProduction());
        $trade = Trade::where('email', "{$email}")
            ->when(app()->isProduction(), function ($query) use ($trade_token) {
                $query->where('token_production', "{$trade_token}");
            })
            ->when(!app()->isProduction(), function ($query) use ($trade_token) {
                $query->where('token_stage', "{$trade_token}");
            })
            ->first();
        return $trade;        
    }

/*    public function showImported(string $trade)
    {

        if (!$trade) {
            return response()->json([
                'message' => 'invalid trade data',
                'code' => 500,
            ], 500);
        }
        switch ($trade) {
            case 'all':
                $trades_object = DB::table('trades_imported')
                //->limit(80) // last 80 orders
                ->get();
                $trades= app(MaintenanceController::class)->object_to_array($trades_object);                
                break;
            // case 'active':
            //     $trades = Trade::where( 'is_program_active', '1')->get();
            //     break;
            default:
                $trades_object =  DB::table('trades_imported')
                        ->where('trade_id', "{$trade}")->get();
                $trades= app(MaintenanceController::class)->object_to_array($trades_object);
                break;
        }

        if (count($trades) == 0) {
            return response()->json([
                'message' => "Error Trade not found",
                'code' => 404,
            ], 404);
        }
        return response()->json($trades, 200); //$trades->toArray()
    }  
        */  
}
