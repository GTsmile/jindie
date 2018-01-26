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
        Log::info(date('y-m-d h:i:',time()).'开始执行同步');
        try{
             $this->tongbuOA();
             $this->tongbuHR();
             $this->tongbuERP();
            Log::info(date('y-m-d h:i:',time()).'同步成功');
        }catch(\Exception $e){
            Log::info(date('y-m-d h:i:',time()).'異常。同步失敗');
        }
    }

    public function tongbuOA(){
        $allPosition=DB::reconnect('pm')->table('relationship')
            ->select('HR_position_id','oa_role_id','HR_position_name','HR_position_departant')
            ->get();    //得到本系统所有职位信息

        foreach($allPosition as $v){
            $userOfPosition=DB::reconnect('sqlsrv')->table('system_position')
                ->join('system_positionmember','system_position.id','=','system_positionmember.position_id')
                ->join('system_depts','system_position.dept_id','=','system_depts.id')
                ->where([
                    ['system_position.name',$v->HR_position_name],
                    ['system_depts.name',$v->HR_position_departant]
                ])
                ->select('system_positionmember.user_id')
                ->get();    //遍历每个职位，得到职位下的所有用户
            $temp=explode(',',$v->oa_role_id);//再将拆开的数组重新组装

            if($userOfPosition){
                foreach($userOfPosition as $vv){
                    $roleOfUser=DB::reconnect('sqlsrv')->table('system_user_role')
                        ->where([
                            ['user_id',$vv->user_id]
                        ])
                        ->select('role_id')
                        ->get()->toArray(); //获取用户的角色信息
                    $arr=[];
                    foreach($roleOfUser as $vvv){    //转换为数组方便对比
                        $arr[]=$vvv->role_id;
                    }
                    $isxiangtong=array_diff($temp,$arr); //比较两个数组是否相同，原系统用户的角色 本系统规定角色
                    if($isxiangtong!=null){
                        echo "不同";
                        $deleteRes=DB::reconnect('sqlsrv')->table('system_user_role')
                            ->where('user_id',$vv->user_id)
                            ->delete();
                        dump($deleteRes);
                        $insertArr=[];
                        if($v->oa_role_id!=""){
                            foreach($temp as $t){
                                $insert=[
                                    'user_id' => $vv->user_id,
                                    'role_id' => $t
                                ];
                                $insertArr[]=$insert;
                            }
                            $insertRes=DB::reconnect('sqlsrv')->table('system_user_role')
                                ->where('user_id',$vv->user_id)
                                ->insert($insertArr);
                            dump($insertRes);
                        }
                    }else{
                        echo "相同";
                    }
                    echo "<br/>";
                }
            }
        }
    }

    public function tongbuHR(){
        $allPosition=DB::reconnect('pm')->table('relationship')
        ->select('HR_position_id','hr_role_id','HR_position_name','HR_position_departant')
        ->get();    //得到本系统所有职位信息

        foreach($allPosition as $v){
            $userOfPosition=DB::reconnect('erp')->table('HRMS_PositionRight')
            ->where([
                ['PositionID',$v->HR_position_id],
            ])
            ->select('RightID')
            ->get()->toArray();    //遍历每个职位，得到职位下的所有用户
            $temp=explode(',',$v->hr_role_id);//再将拆开的数组重新组装 
           
            if($userOfPosition){
                $arr=[];
                foreach($userOfPosition as $vvv){    //转换为数组方便对比
                    $arr[]=$vvv->RightID;
                }
                $isxiangtong=array_diff($arr,$temp);
                if($isxiangtong!=null){
                    echo "不同";
                    $deleteRes=DB::reconnect('erp')->table('HRMS_PositionRight')
                    ->where('PositionID',$v->HR_position_id)
                    ->delete();
                    dump($deleteRes);
                    $insertArr=[];
                    foreach($temp as $t){
                        $insert=[
                            'ID' => $this->guid(),
                            'PositionID' => $v->HR_position_id,
                            'RightID' => $t
                        ];
                        $insertArr[]=$insert;
                    }
                    $insertRes=DB::reconnect('erp')->table('HRMS_PositionRight')
                    ->where('PositionID',$v->HR_position_id)
                    ->insert($insertArr);
                    dump($insertArr);
                }else{
                    echo "相同";
                }
                echo "<br/>";
            }
        }
    }

    public function tongbuERP(){
        $allPosition=DB::reconnect('pm')->table('relationship')
        ->select('HR_position_id','erp_role_id','HR_position_name','HR_position_departant')
        ->get();    //得到本系统所有职位信息

        foreach($allPosition as $v){
            $userOfPosition=DB::reconnect('erp')->table('Wf_biz_InPositionInfo')
            ->join('HR_Base_Emp','Wf_biz_InPositionInfo.EM_ID','=','HR_Base_Emp.EM_ID')
            ->where([
                ['PositionID',$v->HR_position_id],
            ])
            ->select('HR_Base_Emp.Name')
            ->get()->toArray();    //遍历每个职位，得到职位下的所有用户
            $temp=explode(',',$v->erp_role_id);//再将拆开的数组重新组装 
            $userName=[];
            foreach($userOfPosition as $name){
                $userName[]=$name->Name;
            }
            $roleOfUser=DB::reconnect('erp')->table('t_Base_User')
            ->whereIn('t_Base_User.FName',$userName)
            ->select('FUserID')
            ->get()->toArray(); //获取用户的角色信息
                
            if($roleOfUser){
                // dump($roleOfUser);
                foreach($roleOfUser as $vv){
                    $roleOfUser=DB::reconnect('erp')->table('t_GroupAccess')
                    ->where([
                        ['FUserID',$vv->FUserID]
                    ])
                    ->select('FGroupID')
                    ->get()->toArray(); //获取用户的角色信息
                    if($roleOfUser){
                        $arr=[];
                        foreach($roleOfUser as $vvv){
                            $arr[]=$vvv->FGroupID;;
                        }
                        $isbutong=array_diff($arr,$temp);
                        if($isbutong!=null){
                            echo "不同";
                            $deleteRes=DB::reconnect('erp')->table('t_GroupAccess')
                            ->where('FUserID',$vv->FUserID)
                            ->delete();
                            dump($deleteRes);
                            $insertArr=[];
                            foreach($temp as $t){
                                $insert=[
                                    'FUserID' => $vv->FUserID,
                                    'FGroupID' => $t,
                                    'FType' => 1
                                ];
                                $insertArr[]=$insert;
                            }
                            $insertRes=DB::reconnect('erp')->table('t_GroupAccess')
                            ->where('FUserID',$vv->FUserID)
                            ->insert($insertArr);
                            dump($insertArr);
                        }else{
                            echo "相同";
                        }
                        echo "<br/>";
                    }
                }
            }
        }
    }

       //生成GUID
    function guid(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = 
                    substr($charid, 0, 8).$hyphen
                    .substr($charid, 8, 4).$hyphen
                    .substr($charid,12, 4).$hyphen
                    .substr($charid,16, 4).$hyphen
                    .substr($charid,20,12);
                    
            return $uuid;
        }
    }
}
