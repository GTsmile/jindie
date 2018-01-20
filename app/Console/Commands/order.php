<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use DB;
class order extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:make';

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
         $result=DB::reconnect('pm')->table('system_users')->where('id',2)->update(['sex'=> 9]);
        //
        //Log::info('test');
    }
}
