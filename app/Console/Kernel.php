<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Psy\Command\Command;
use Log;
use DB;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\order::class,
    ];

    /**
     * Define the application's command schedule.
     *】
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /*Log::info('开始执行');
        try{
            $result=DB::reconnect('sqlsrv')->table('system_users')->where('id',2)->update(['sex'=> 8]);
            Log::info($result);
        }catch (Exception $e){
            Log::info($e);
        }
        Log::info('执行成功');*/
        $schedule->command('order')->everyMinute();

    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
