<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Session;
use Log;
class IndexController extends Controller{
    //得到用户模块所有用户信息（OA系统）
    public function getUser(Request $request){
        $currentPage=$request->input('currentPage');
        $pageSize=$request->input('pageSize');
        $offset=($currentPage-1)*$pageSize;
        $where=$request->input('where');
        if($where!=""){
            $select_row=DB::reconnect('sqlsrv')->table('system_users')
            ->join('system_positionmember','system_positionmember.user_id','=','system_users.id')
            ->join('system_position','system_positionmember.Position_id','=','system_position.id')
            ->join('system_depts','system_depts.id','=','system_users.dept_id')
            ->select('system_users.username as name', 'system_position.name as position', 'system_depts.name as department')
            ->where('system_users.username',$where)
            ->orWhere('system_position.name',$where)
            ->orWhere('system_depts.name',$where)
            ->offset($offset)
            ->limit($pageSize)
            ->get();

            $userCount=DB::reconnect('sqlsrv')->table('system_users')
            ->join('system_positionmember','system_positionmember.user_id','=','system_users.id')
            ->join('system_position','system_positionmember.Position_id','=','system_position.id')
            ->join('system_depts','system_depts.id','=','system_users.dept_id')
            ->select('system_users.username as name', 'system_position.name as position', 'system_depts.name as department')
            ->where('system_users.username',$where)
            ->orWhere('system_position.name',$where)
            ->orWhere('system_depts.name',$where)
            ->count();
        }else{
            $select_row=DB::reconnect('sqlsrv')->table('system_users')
            ->join('system_positionmember','system_positionmember.user_id','=','system_users.id')
            ->join('system_position','system_positionmember.Position_id','=','system_position.id')
            ->join('system_depts','system_depts.id','=','system_users.dept_id')
            ->select('system_users.username as name', 'system_position.name as position', 'system_depts.name as department')
            ->offset($offset)
            ->limit($pageSize)
            ->get();

            $userCount=DB::reconnect('sqlsrv')->table('system_users')
            ->join('system_positionmember','system_positionmember.user_id','=','system_users.id')
            ->join('system_position','system_positionmember.Position_id','=','system_position.id')
            ->join('system_depts','system_depts.id','=','system_users.dept_id')
            ->select('system_users.username as name', 'system_position.name as position', 'system_depts.name as department')
            ->count();
        }

        return  response()->json([
            'select_row' => $select_row,
            'userCount' => $userCount
        ]);
    }

    public function updateLocUserRole(){
            
    }

    //获取权限模块所有职位信息
    public function getPositionUnit(Request $request){
        $currentPage=$request->input('currentPage');
        $pageSize=$request->input('pageSize');
        $where = $request->input('where');

        $offset=($currentPage-1)*$pageSize;
        if($where!=""){
            $select_row  =DB::reconnect('pm')->table('relationship')
            ->select('HR_position_id as id', 'HR_position_name as position', 'HR_position_departant as department', 'oa_role_id', 'erp_role_id', 'crm_role_id', 'scm_role_id', 'plm_role_id', 'hr_role_id')
            ->where('HR_position_name',$where)
            ->offset($offset)
            ->limit($pageSize)
            ->get();
            $positionCount  =DB::reconnect('pm')->table('relationship')->where('HR_position_name',$where)->count();
        }else{
            $select_row  =DB::reconnect('pm')->table('relationship')
            ->select('HR_position_id as id', 'HR_position_name as position', 'HR_position_departant as department', 'oa_role_id', 'erp_role_id', 'crm_role_id', 'scm_role_id', 'plm_role_id', 'hr_role_id')
            ->offset($offset)
            ->limit($pageSize)
            ->get();
            $positionCount  =DB::reconnect('pm')->table('relationship')->count();
        }
        foreach($select_row as $val) {
            if($val->oa_role_id !=null) $val->oa_role_id = explode(',',$val->oa_role_id); 
            if($val->erp_role_id !=null) $val->erp_role_id = explode(',',$val->erp_role_id); 
            if($val->crm_role_id !=null) $val->crm_role_id = explode(',',$val->crm_role_id); 
            if($val->scm_role_id !=null) $val->scm_role_id = explode(',',$val->scm_role_id); 
            if($val->plm_role_id !=null) $val->plm_role_id = explode(',',$val->plm_role_id); 
            if($val->hr_role_id !=null)  $val->hr_role_id = explode(',',$val->hr_role_id); 
        }

        

        return response()->json([
            'select_row' => $select_row,
            'positionCount' => $positionCount
        ]);
    }

    //获取各个系统不同角色信息（权限信息）
    public function getRole(){
        //OA role
        $oa=DB::reconnect('sqlsrv')->table("system_roles")->get();
        $erp=DB::reconnect('erp')->table("t_GroupAccessType")->get();
        $hr=DB::reconnect('erp')->table("HRMS_Permissions")->get();
        return response()->json([
            'roleOA'=> $oa,
            'roleERP'=>$erp,
            'roleHR'=>$hr
        ]);
    }

    //更新OA用户角色主接口
    public function updateRole(Request $request){
        $selRoleIndex=implode(",",$request->input('selRoleIndex'));
        $db=$request->input('db');
        $positionId=$request->input('currentPositionId');
        $positionName=$request->input('currentPositionName');
        $departmentName=$request->input('currentDepartment');
        $time=date('y-m-d h:i:',time());
        if($db=="OA"){
            DB::beginTransaction();
            try{
                $result=DB::reconnect('pm')->table('relationship')
                ->where('HR_position_id',$positionId)
                ->update(['oa_role_id '=> $selRoleIndex]);
                $userOfPosition=$this->userOfPosition($positionName,$departmentName);
                $delres=$this->deleteOAUser(array_column(json_decode($userOfPosition,'true'),'user_id'));
                $inserRes=$this->insertUserRole(array_column(json_decode($userOfPosition,'true'),'user_id'),$request->input('selRoleIndex'));
                if($result&&$inserRes){  
                    DB::commit();  
                    Log::info($time.session('user')->username.$db.$positionName.$departmentName.$selRoleIndex.$positionId.'true');                                        
                    return response()->json("true");
                }  
            }catch(\Exception $e){
                DB::rollBack();
            }
            Log::info($time.session('user')->username.$db.$positionName.$departmentName.$selRoleIndex.$positionId.'false');                                
            return  response()->json("false");
        }else if($db=="ERP"){
            DB::beginTransaction();
            try{    //得到该职位在hr系统里的用户,然后再erp系统里找到这些用户并添加对应的权限（如果身份证号信息没填就没法对应，名字的话重名就完了）
                $result=DB::reconnect('pm')->table('relationship')
                ->where('HR_position_id',$positionId)
                ->update(['erp_role_id '=> $selRoleIndex]);
                $userOfIDCard=$this->userOfPositionERP($positionName,$departmentName);
                $userIdRes=$this->idCardofuserID((array_column(json_decode($userOfIDCard,'true'),'IDCardID')));   
                $delres=$this->deleteERPUser(array_column(json_decode($userIdRes,'true'),'FUserID'));
                $inserRes=$this->insertUserRoleERP(array_column(json_decode($userIdRes,'true'),'FUserID'),$request->input('selRoleIndex'));
                if($result&&$inserRes){  
                    DB::commit();  
                    Log::info($time.session('user')->username.$db.$positionName.$departmentName.$selRoleIndex.$positionId.'true');                                        
                    return response()->json("true");
                }  
            }catch(\Exception $e){
                DB::rollBack();
            }
            Log::info($time.session('user')->username.$db.$positionName.$departmentName.$selRoleIndex.$positionId.'false');                                
            return  response()->json("false");
        }else if($db=="HR"){
            DB::beginTransaction();
            try{
                $result=DB::reconnect('pm')->table('relationship')
                ->where('HR_position_id',$positionId)
                ->update(['hr_role_id '=> $selRoleIndex]);
                $delHRPer=DB::reconnect('erp')->table('HRMS_PositionRight')->where('PositionID',$positionId)->delete();
                $inserRes=$this->insertHRauth($positionId,$selRoleIndex);
                if($result&&$inserRes){  
                    DB::commit();  
                    Log::info($time.session('user')->username.$db.$positionName.$departmentName.$selRoleIndex.$positionId.'true');                                        
                    return response()->json("true");
                }  
            }catch(\Exception $e){
                DB::rollBack();
            }
            Log::info($time.session('user')->username.$db.$positionName.$departmentName.$selRoleIndex.$positionId.'false');                    
            return  response()->json("false");
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
            $uuid = chr(123)// "{"
                    .substr($charid, 0, 8).$hyphen
                    .substr($charid, 8, 4).$hyphen
                    .substr($charid,12, 4).$hyphen
                    .substr($charid,16, 4).$hyphen
                    .substr($charid,20,12)
                    .chr(125);// "}"
            return $uuid;
        }
    }

    //HR系统权限插入 参数：职位id,权限id数组
    public function insertHRauth($positionId,$authStr){
        $rowAll=[];
        if($authStr=="") return true;
        $authArr=explode(',',$authStr);
        foreach($authArr as $authId){
            $row=[
                'ID' => $this->guid(),
                'PositionID' => $positionId,
                'RightID' => $authId
            ];
            $rowAll []=$row;
        }
        $res=DB::reconnect('erp')->table('HRMS_PositionRight')->insert($rowAll);
        if($res){
            return true;
        }
        return false;
    }


    //用户Id数组（通过配置职位角色，得到该职位所有用户的id）
    //OA角色id数组在OA系统里插入用户对应角色（权限）
    public function insertUserRole($userIdArr,$selRoleIndex){
        $rowAll=[];
        foreach($userIdArr as $userId){
            foreach($selRoleIndex as $roleIndex){
                $row = [
                    'user_id' => $userId,
                    'role_id' => $roleIndex
                ];
                $rowAll[] = $row;
            } 
        }
        $res=DB::reconnect('sqlsrv')->table('system_user_role')->insert($rowAll);
        if($res){
            return true;
        }
        return false;
    }

    //用户Id数组（通过配置职位角色，得到该职位所有用户的id）
    //ERP权限id数组在OA系统里插入用户对应角色（权限）
    public function insertUserRoleERP($userIdArr,$selRoleIndex){
        $rowAll=[];
        foreach($userIdArr as $userId){
            foreach($selRoleIndex as $roleIndex){
                $row = [
                    'FUserID' => $userId,
                    'FGroupID' => $roleIndex,
                    'FType'=> 1
                ];
                $rowAll[] = $row;
            } 
        }
        $res=DB::reconnect('erp')->table('t_GroupAccess')->insert($rowAll);
        if($res){
            return true;
        }
        return false;
    }

    //传入用户id数组将system_user_role该用户们的所有角色信息删除
    public function deleteOAUser($userIdArr){
        $res=DB::reconnect('sqlsrv')->table('system_user_role')
        ->whereIn('user_id',$userIdArr)
        ->delete();
        return $res;
    }

    //根据身份证号得到用户Id        ERP
    public function idCardofuserID($userIdCardArr){
        $res = DB::reconnect('erp')->table('t_Base_Emp')
        ->join('t_Base_User','t_Base_User.FEmpID','=','t_Base_Emp.FItemID')
        ->whereIn('FID',$userIdCardArr)
        ->select('t_Base_User.FUserID')
        ->get();
        return $res;
    }

    //传入用户id数组将system_user_role该用户们的所有权限信息删除  ERP
    public function deleteERPUser($userIdArr){
        $res=DB::reconnect('erp')->table('t_GroupAccess')
        ->whereIn('FUserID',$userIdArr)
        ->delete();
        return $res;
    }

    //传入职位名称，得到该职位所有用户身份证号     ERP
    public function userOfPositionERP($positionName,$departmentName){
        $userOfPosition=DB::reconnect('erp')->table('HR_Base_Emp')
        ->join('Wf_biz_InPositionInfo','Wf_biz_InPositionInfo.EM_ID','=','HR_Base_Emp.EM_ID')
        ->join('ORG_Position','Wf_biz_InPositionInfo.PositionID','=','ORG_Position.ID')
        ->join('ORG_Unit','ORG_Unit.ID','=','Wf_biz_InPositionInfo.UnitID')
        ->where([
            ['ORG_Position.Name',$positionName],
            ['ORG_Unit.Name',$departmentName]
        ])
        ->select('HR_Base_Emp.IDCardID')
        ->groupBy('HR_Base_Emp.IDCardID')
        ->get();
        return  $userOfPosition;
    }

    //传入职位名称，得到该职位所有用户id      OA
    public function userOfPosition($positionName,$departmentName){
        $userOfPosition=DB::reconnect('sqlsrv')->table('system_position')
        ->join('system_positionmember','system_position.id','=','system_positionmember.position_id')
        ->join('system_depts','system_position.dept_id','=','system_depts.id')
        ->where([
            ['system_position.name',$positionName],
            ['system_depts.name',$departmentName]
        ])
        ->select('system_positionmember.user_id')
        ->get();
        return  $userOfPosition;
    }
}