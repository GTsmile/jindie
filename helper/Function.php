<?php
/**
 * Created by PhpStorm.
 * User: gaocuili
 * Date: 2017/11/11
 * Time: 下午6:55
 */

function responseToJson($code=0,$msg="",$result=null){
    $res["code"] = $code;
    $res["msg"] = $msg;
    $res["result"] = $result;
    return response()->json($res);
}