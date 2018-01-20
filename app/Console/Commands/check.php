<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\TimeController;
use Illuminate\Console\Command;
use DB;

class check extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        //Log:info('test');
        //TimeController::start();
        $result = DB::table('system_users')->where('id',2)->update(['sex'=>5]);
        dump($result);
    }
}
