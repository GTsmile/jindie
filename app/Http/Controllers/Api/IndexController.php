<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;

class IndexController extends Controller{

    public function getUser(Request $request){
        $currentPage=$request->input('currentPage');
        $pageSize=$request->input('pageSize');
        $offset=($currentPage-1)*$pageSize;
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

        return  response()->json([
            'select_row' => $select_row,
            'userCount' => $userCount
        ]);
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

    //把HR系统职位信息统计进入本地过渡表
    public function updateLocMes(){
         $select_row  =DB::reconnect('erp')->table('ORG_Position')
        ->join('Wf_biz_InPositionInfo','Wf_biz_InPositionInfo.PositionID','=','ORG_Position.ID')
        ->join('ORG_Unit','Wf_biz_InPositionInfo.UnitID','=','ORG_Unit.ID')
        ->select('ORG_Position.ID as HR_position_id', 'ORG_Position.Name as HR_position_name', 'ORG_Unit.Name as HR_position_department')
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

    public function getPositionUnit(Request $request){
       


        $currentPage=$request->input('currentPage');
        $pageSize=$request->input('pageSize');
        $offset=($currentPage-1)*$pageSize;
        $select_row  =DB::reconnect('pm')->table('relationship')
        ->select('HR_position_id as id', 'HR_position_name as position', 'HR_position_departant as department', 'oa_role_id', 'erp_role_id', 'crm_role_id', 'scm_role_id', 'plm_role_id', 'hr_role_id')
        ->offset($offset)
        ->limit($pageSize)
        ->get();

        foreach($select_row as $val) {
            if($val->oa_role_id !=null) $val->oa_role_id = explode(',',$val->oa_role_id); 
            if($val->erp_role_id !=null) $val->erp_role_id = explode(',',$val->erp_role_id); 
            if($val->crm_role_id !=null) $val->crm_role_id = explode(',',$val->crm_role_id); 
            if($val->scm_role_id !=null) $val->scm_role_id = explode(',',$val->scm_role_id); 
            if($val->plm_role_id !=null) $val->plm_role_id = explode(',',$val->plm_role_id); 
            if($val->hr_role_id !=null)  $val->hr_role_id = explode(',',$val->hr_role_id); 
        }

        $positionCount  =DB::reconnect('pm')->table('relationship')->count();

        return response()->json([
            'select_row' => $select_row,
            'positionCount' => $positionCount
        ]);
    }

    public function getRole(){
        //OA role
        $oa=DB::reconnect('sqlsrv')->table("system_roles")->get();
        return response()->json([
            'roleOA'=> $oa
        ]);
    }

    //更新OA用户角色主接口
    public function updateRole(Request $request){
        $selRoleIndex=implode(",",$request->input('selRoleIndex'));
        $db=$request->input('db');
        $positionId=$request->input('currentPositionId');
        $positionName=$request->input('currentPositionName');
        $departmentName=$request->input('currentDepartment');
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
                    return response()->json("true");
                }  
            }catch(\Exception $e){
                DB::rollBack();
            }
            return  response()->json("false");
        }
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

    //传入用户id数组将system_user_role该用户们的所有角色信息删除
    public function deleteOAUser($userIdArr){
        $res=DB::reconnect('sqlsrv')->table('system_user_role')
        ->whereIn('user_id',$userIdArr)
        ->delete();
        return $res;
    }

    //传入职位名称，得到该职位所有用户id
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