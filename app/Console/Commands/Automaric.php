<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class Automaric extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '尝试计划任务';

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
        //  $result=DB::reconnect('pm')->table('relationship')
        //     ->where('HR_position_id',"B8215F7A-816D-4FE1-9F57-0008C4FEDD99")
        //     ->update(['HR_position_name '=> "测试"]);
        // DB::reconnect('pm')->select('select * from relationship');
        // echo "Hello artisan";
    }
}
