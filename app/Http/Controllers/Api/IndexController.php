<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;

class IndexController extends Controller{

    public function getUser(){
        $select_row  =DB::reconnect('erp')->table('Wf_biz_InPositionInfo')
        ->join('HR_Base_Emp','Wf_biz_InPositionInfo.EM_ID','=','HR_Base_Emp.EM_ID')
        ->join('ORG_Position','Wf_biz_InPositionInfo.PositionID','=','ORG_Position.ID')
        ->join('ORG_Unit','Wf_biz_InPositionInfo.UnitID','=','ORG_Unit.ID')
        ->select('HR_Base_Emp.Name as name', 'ORG_Position.Name as position', 'ORG_Unit.Name as department')
        ->get();
        return response()->json($select_row);
    }

    public function getRole(){
        //OA role
        $select_row=DB::table("system_roles")->get();
        return response()->json($select_row);
    }

}