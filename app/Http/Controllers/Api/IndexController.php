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

    public function getPositionUnit(Request $request){
        // $currentPage=$request->input('currentPage');
        // $pageSize=$request->input('pageSize');
        // $offset=($currentPage-1)*$pageSize;
        // $select_row  =DB::reconnect('erp')->table('Wf_biz_InPositionInfo')
        // ->join('ORG_Position','Wf_biz_InPositionInfo.PositionID','=','ORG_Position.ID')
        // ->join('ORG_Unit','Wf_biz_InPositionInfo.UnitID','=','ORG_Unit.ID')
        // ->select('ORG_Position.ID as id', 'ORG_Position.Name as position', 'ORG_Unit.Name as department')
        // ->offset($offset)
        // ->limit($pageSize)
        // ->get();

        $currentPage=$request->input('currentPage');
        $pageSize=$request->input('pageSize');
        $offset=($currentPage-1)*$pageSize;
        $select_row  =DB::reconnect('pm')->table('relationship')
        ->select('HR_position_id as id', 'HR_position_name as position', 'HR_position_departant as department', 'oa_role_id', 'erp_role_id', 'crm_role_id', 'scm_role_id', 'plm_role_id', 'hr_role_id')
        ->offset(0)
        ->limit(10)
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

    public function updateRole(Request $request){
        $selRoleIndex=implode(",",$request->input('selRoleIndex'));
        $db=$request->input('db');
        $positionId=$request->input('currentPositionId');
        $positionName=$request->input('currentPositionName');
        if($db=="OA"){
            DB::beginTransaction();
            try{
                $result=DB::reconnect('pm')->table('relationship')
                ->where('HR_position_id',$positionId)
                ->update(['oa_role_id '=> $selRoleIndex]);
                $userOfPosition=$this->userOfPosition($positionName);
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
        $res=DB::reconnect('sqlsrv')->table('system_user_role')
        ->insert($rowAll);
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
    public function userOfPosition($positionName){
        $userOfPosition=DB::reconnect('sqlsrv')->table('system_position')
        ->join('system_positionmember','system_position.id','=','system_positionmember.position_id')
        ->where('system_position.name',$positionName)
        ->select('system_positionmember.user_id')
        ->get();
        return  $userOfPosition;
    }
}