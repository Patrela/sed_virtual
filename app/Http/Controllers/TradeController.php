<?php

namespace App\Http\Controllers;


use App\Models\Trade;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


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
}
