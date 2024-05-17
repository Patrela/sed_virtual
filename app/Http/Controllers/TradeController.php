<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trades = Trade::orderBy('id')->get();
        return response()->json($trades, 200);

    }

    public function idExists($code)
    {
        $trade = Trade::where('trade_id', $code)->first();
        return  ($trade)? 1 : 0;
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Trade $trade)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trade $trade)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trade $trade)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trade $trade)
    {
        //
    }
}
