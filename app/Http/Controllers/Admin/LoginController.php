<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use DB;
use App\Models\Admin;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Http\Request;
use Session;
class LoginController extends Controller
{
    public function check(Request $request)
    {
        $user = $request->user;
        $captcha = $request->captcha;
        $errCount=$request->errCount;
        if($request->isMethod('post')){
            if (session('captcha') == $captcha||$errCount<=3) {
                //用户输入验证码正确
                $password = md5($request->password);
                $result = Admin::check_login($user);
                if($result){
                    if($password == $result->password){
                        session(['user' => $result]);
                        return responseToJson(0,'success','登录成功');
                    }else{
                        return responseToJson(1,'error','密码错误');
                    }
                }else{
                    return responseToJson(2,'error','该用户不存在');
                }
            } else {
                //用户输入验证码错误
                return responseToJson(3, $captcha, session('captcha'));
            }
        }
    }

    /**
     * 获取验证码
     */
    public function get_captcha()
    {
        $builder=new CaptchaBuilder();
        $builder ->build(100,40);
        $phrase = $builder->getPhrase();  // 获取验证码内容
        Session::flash('captcha', $phrase);
        return response($builder->output())->header('Content-type','image/jpeg');
    }

    public function logout(Request $request)
    {
        $result = $request->session()->flush();
        return responseToJson(0,'success','退出成功');
    }

    //更新数据到本地  第一句不要重复执行
    public function test()
    {
        // $this->updateLocMes();
        // $this->tongbuOArole();
        // $this->tongbuHRrole();
        // $this->tongbuERProle();
    }

     //把HR系统职位信息统计进入本地过渡表    部门职位等
    public function updateLocMes(){
        $select_row  =DB::reconnect('erp')->table('ORG_Position')
       ->join('ORG_Unit','ORG_Position.UnitID','=','ORG_Unit.ID')
       ->select('ORG_Position.ID as HR_position_id', 'ORG_Position.Name as HR_position_name', 'ORG_Unit.Name as HR_position_department')
       ->groupBy('ORG_Position.ID','ORG_Position.Name','ORG_Unit.Name')
       ->get()
       ->toArray();
       $rowa=[];
       foreach($select_row as $val){
           $row=[
               'HR_position_id' => $val->HR_position_id,
               'HR_position_name'=> $val->HR_position_name,
               'HR_position_departant'=> $val->HR_position_department
           ];
           $rowa[]=$row;
       }
       $se=$this->array_unique_fb($rowa);
       foreach($se as $val){
           $row=[
               'HR_position_id' => $val[0],
               'HR_position_name'=> $val[1],
               'HR_position_departant'=> $val[2]
           ];
           $res = DB::reconnect('pm')->table('relationship')->insert($row);
           dump($res);
       }
   }

    //传入一个二维数组，去除重复值
    function array_unique_fb($array2D){   
        foreach ($array2D as $v){ 
            $v=join(',',$v);//降维,也可以用implode,将一维数组转换为用逗号连接的字符串 
            $temp[]=$v; 
        } 
        $temp=array_unique($temp);//去掉重复的字符串,也就是重复的一维数组 
        foreach ($temp as $k => $v){ 
            $temp[$k]=explode(',',$v);//再将拆开的数组重新组装 
        } 
        return $temp; 
    } 

    //把hr系统角色同步过来
    public function tongbuHRrole(){
        $userOfPosition=DB::reconnect('erp')->table('ORG_Position')
        ->select('ID')
        ->get();
        foreach($userOfPosition as $v){
            $res=DB::reconnect('erp')->table('HRMS_PositionRight')
            ->where('PositionID',$v->ID)
            ->select('RightID')
            ->get();
            $role="";
            foreach($res as $vv){
                $role.=$vv->RightID.",";
            }
            $newstr = substr($role,0,strlen($role)-1);
            $r=DB::reconnect('pm')
            ->table('relationship')
            ->where('HR_position_id',$v->ID)
            ->update(['hr_role_id '=> $newstr]);     
            dump($r);         
        }
    }

     //把erp系统角色同步过来
     public function tongbuERProle(){
        $allPosition=DB::reconnect('erp')->table('t_GroupAccess')
        ->select('FUserID')
        ->groupBy('FUserID')
        ->get();
        foreach($allPosition as $v){
            $userOfPosition=DB::reconnect('erp')->table('t_Base_User')
            ->join('t_Base_Emp','t_Base_User.FEmpID','=','t_Base_Emp.FItemID')
            ->select('t_Base_Emp.FID')
            ->where('t_Base_User.FUserID',$v->FUserID)
            ->get();
            $userOfAuth=DB::reconnect('erp')->table('t_GroupAccess')
            ->select('FGroupID')
            ->where('FUserID',$v->FUserID)
            ->get();
            $role="";
            foreach($userOfAuth as $u){
                $role.=$u->FGroupID.",";
            }
            $newstr = substr($role,0,strlen($role)-1); 
            if(count($userOfPosition)){
                $Position=DB::reconnect('erp')->table('Wf_biz_InPositionInfo')
                ->join('HR_Base_Emp','HR_Base_Emp.EM_ID','=','Wf_biz_InPositionInfo.EM_ID')
                ->join('ORG_Position','Wf_biz_InPositionInfo.PositionID','=','ORG_Position.ID')
                ->select('ORG_Position.ID')
                ->where('HR_Base_Emp.IDCardID',$userOfPosition[0]->FID)
                ->get();
                foreach($Position as $p){
                    $r=DB::reconnect('pm')
                    ->table('relationship')
                    ->where('HR_position_id',$p->ID)
                    ->update(['erp_role_id '=> $newstr]);  
                    dump($r);
                }
            }
        }
    }

    //把oa系统角色同步过来
    public function tongbuOArole(){
        $allPosition=DB::reconnect('sqlsrv')->table('system_position')
        ->join('system_depts','system_position.dept_id','=','system_depts.id')
        ->select('system_position.name as position','system_depts.name as dept')
        ->groupBy('system_position.name ','system_depts.name')
        ->get();
        foreach($allPosition as $v){
            $userOfPosition=DB::reconnect('sqlsrv')->table('system_position')
            ->join('system_positionmember','system_position.id','=','system_positionmember.position_id')
            ->join('system_depts','system_position.dept_id','=','system_depts.id')
            ->where([
                ['system_position.name',$v->position],
                ['system_depts.name',$v->dept]
            ])
            ->select('system_positionmember.user_id')
            ->first();
            if($userOfPosition!=null){
                $res=DB::reconnect('sqlsrv')->table('system_user_role')
                ->where('user_id',$userOfPosition->user_id)
                ->get();
                $role="";
                foreach($res as $vv){
                    $role.=$vv->role_id.",";
                }
                $newstr = substr($role,0,strlen($role)-1); 
                $r=DB::reconnect('pm')
                ->table('relationship')
                ->where([
                    ['HR_position_name',$v->position],
                    ['HR_position_departant',$v->dept]
                ])
                ->update(['oa_role_id '=> $newstr]);     
                dump($r);
            }
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