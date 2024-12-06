<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\SedController;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class CreateNewUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        app(SedController::class)->updateNewUsers();
        app(SedController::class)->getTradeUsers();
        // TODO:disable until Epicor update Staff emails.
        // Meanwhile, put new users in table users_staff. Store Procedure sp_import_users proccesses all through updateNewUsers job.
        // After Epicor resolves the issue, eliminate the DB Store Procedure sp_import_users() execution. It means, eliminate updateStaffUsers()
        //app(SedController::class)->getStaffUsers();
        app(SedController::class)->updateStaffUsers();
    }
}
