<?php

namespace App\Http\Controllers;

use App\Models\UserVisitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserVisitLogController extends Controller
{
    public function userVisitRegistry($userid, $usertradeid = null)
    {
        UserVisitLog::create(
            [
                'user_id' => $userid,
                'trade_id' => $usertradeid,
                'log_date' => date("Y-m-d")
            ]
        );
    }

    public function index()
    {
        $visits_object = DB::table('view_trade_visit_log')
                            ->get();
        $visits = app(MaintenanceController::class)->object_to_array($visits_object);
        return $visits;
    }

    public function getUserVisitLogs(Request $request, string $email)
    {
        $startdate = $request->header('x-api-start');
        $enddate = $request->header('x-api-end');
        $visits_object = DB::table('view_user_visit_log')
            ->where('email', $email)
            ->whereBetween('log_date', ["{$startdate}", "{$enddate}"])
            // ->when($startdate !== '', function ($query) use ($startdate) {
            //     $query->where('log_date','>=', "{$startdate}");
            //     })
            // ->when($month !== '0', function ($query) use ($month) {
            //     $query->where('month_log', $month);
            //     })
            // ->when($day !== '0', function ($query) use ($day) {
            //     $query->where('day_log', $day);
            //     })
            // ->orderByDesc('year_log')
            // ->orderByDesc('month_log')
            ->get();
        $visits = app(MaintenanceController::class)->object_to_array($visits_object);
        return $visits;
        //return response()->json($visits, 200);
    }

    public function getTradeVisitLogs(Request $request, string $nit = 'all')
    {
        $startdate = $request->header('x-api-start');
        $enddate = $request->header('x-api-end');
        Log::info("Getting  trade visits. startdate= {$startdate} enddate= {$enddate}");
        $visits_object = DB::table('view_trade_visit_log')
            ->when($nit !== 'all', function ($query) use ($nit, $startdate, $enddate) {
                $query->where('nit', $nit)
                    ->whereBetween('log_date', ["{$startdate}", "{$enddate}"]);
                })
            //->whereBetween('log_date', ["{$startdate}", "{$enddate}"])


            // ->when($startdate !== '', function ($query) use ($startdate) {
            //     $query->where('log_date','>=', "{$startdate}");
            //     })
            // ->when($month !== '0', function ($query) use ($month) {
            //     $query->where('month_log', $month);
            //     })
            // ->when($day !== '0', function ($query) use ($day) {
            //     $query->where('day_log', $day);
            //     })
            // ->orderByDesc('year_log')
            // ->orderByDesc('month_log')
            ->get();

        //$logs = UserVisitLog::whereBetween('log_date', ["{$startdate}", "{$enddate}"])->get()->toArray();


        // $visits_object = DB::table('view_trade_visit_log')
        //             ->when($nit !== 'all', function ($query) use ($nit) {
        //                 $query->where('nit', "{$nit}");
        //             })
        //             ->when($year !== '0', function ($query) use ($year) {
        //                 $query->where('year_log', $year);
        //             })
        //             ->when($month !== '0', function ($query) use ($month) {
        //                 $query->where('month_log', $month);
        //             })
        //             ->when($day !== '0', function ($query) use ($day) {
        //                 $query->where('day_log', $day);
        //             })
        //             ->orderBy('trade')
        //             ->orderByDesc('year_log')
        //             ->orderByDesc('month_log')
        //             ->get();

        // $visits= app(MaintenanceController::class)->object_to_array($visits_object);
        // //Log::info('view_trade_visit_log  ',$visits);
        //return $logs;

        $visits = app(MaintenanceController::class)->object_to_array($visits_object);
        return $visits;
    }

    public function getVisitLogs(Request $request, string $item = "all")
    {
        $startdate = $request->header('x-api-start');
        $enddate = $request->header('x-api-end');
        $start = (!$startdate)? "2024-01-01": $startdate;
        $end = !$enddate? "2030-12-31" : $enddate;

        Log::info("getVisitLogs. startdate= {$startdate} enddate= {$enddate}");
        // email use view_trade_visit_log - trade use view_trade_visit_log
        if(!str_contains($item, "@")){
            $visits_object = DB::table('view_trade_visit_log')
            ->when($item !== 'all', function ($query) use ($item,  $start, $end) {
                $query->where('nit', "{$item}")
                ->whereBetween('log_date', ["{$start}", "{$end}"]);
                })
            ->get();
        }
        else{
            $visits_object = DB::table('view_user_visit_log')
            ->where('email', "{$item}")
            ->whereBetween('log_date', ["{$start}", "{$end}"])
            ->get();
        }

        $visits = app(MaintenanceController::class)->object_to_array($visits_object);
        Log::info("getVisitLogs. visits", $visits);
        return $visits;
    }

}
