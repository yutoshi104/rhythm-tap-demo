<?php

trait LogDb {

    ####################################################################################################
    # admin_logテーブル
    ####################################################################################################

    # 管理者ログ登録
    public function insertAdminLog($action, $admin_id, $detail="", $status=0, $ip=null, $ua=null, $url=null) {
        if(is_null($ip)){
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if(is_null($ua)){
            $ua = $_SERVER['HTTP_USER_AGENT'];
        }
        if(is_null($url)){
            $url = $_SERVER['REQUEST_URI'];
        }
        return $this->insert("admin_log", ["admin_id"=>$admin_id,"ip"=>$ip,"ua"=>$ua,"action"=>$action,"detail"=>$detail,"url"=>$url,"status"=>$status], true, false);
    }
    # 管理者ログ取得
    public function getAdminLog($admin_id=null,$action="",$detail="",$s=2,$sort_num=0,$from=0,$num=ADMIN_PAGING_CHECK){
        $where = [];
        if($admin_id!==null){
            $where[] = ["admin_id","=",$admin_id];
        }
        if($action!==""){
            $where[] = ["action","LIKE","%".$action."%"];
        }
        if($detail!==""){
            $where[] = ["detail","LIKE","%".$detail."%"];
        }
        if($s===0){
            $where[] = ["status","=",0];
        }else if($s===1){
            $where[] = ["status","=",1];
        }
        $res = $this->fetch("admin_log", "*,(SELECT admin_name FROM admin WHERE admin_id=admin_log.admin_id) as admin_name", $where, ADMIN_SORT_PATERN[$sort_num][0], ["offset"=>$from,"row_count"=>$num]);
        if($res){
            return $res->fetch_all(MYSQLI_ASSOC);
        }else{
            return null;
        }
    }
    # 管理者ログ件数取得
    public function getAdminLogNum($admin_id=null,$action="",$detail="",$s=2){
        $where = [];
        if($admin_id!==null){
            $where[] = ["admin_id","=",$admin_id];
        }
        if($action!==""){
            $where[] = ["action","LIKE","%".$action."%"];
        }
        if($detail!==""){
            $where[] = ["detail","LIKE","%".$detail."%"];
        }
        if($s===0){
            $where[] = ["status","=",0];
        }else if($s===1){
            $where[] = ["status","=",1];
        }
        $res = $this->fetch("admin_log", "COUNT(*) as num", $where);
        if($res){
            return $res->fetch_array(MYSQLI_ASSOC)['num'];
        }else{
            return null;
        }
    }

    ####################################################################################################



    ####################################################################################################
    # user_logテーブル
    ####################################################################################################

    # ユーザーログ登録
    public function insertUserLog($action, $user_id=null, $detail="", $status=0, $ip=null, $ua=null, $url=null) {
        if(is_null($ip)){
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if(is_null($ua)){
            $ua = $_SERVER['HTTP_USER_AGENT'];
        }
        if(is_null($url)){
            $url = $_SERVER['REQUEST_URI'];
        }
        return $this->insert("user_log", ["user_id"=>$user_id,"ip"=>$ip,"ua"=>$ua,"action"=>$action,"detail"=>$detail,"url"=>$url,"status"=>$status], true, false);
    }
    # ユーザーログ取得
    public function getUserLog($user_id=null,$action="",$detail="",$s=2,$sort_num=0,$from=0,$num=ADMIN_PAGING_CHECK){
        $where = [];
        if($user_id!==null){
            $where[] = ["user_id","=",$user_id];
        }
        if($action!==""){
            $where[] = ["action","LIKE","%".$action."%"];
        }
        if($detail!==""){
            $where[] = ["detail","LIKE","%".$detail."%"];
        }
        if($s===0){
            $where[] = ["status","=",0];
        }else if($s===1){
            $where[] = ["status","=",1];
        }
        $res = $this->fetch("user_log", "*,(SELECT user_name FROM user WHERE user_id=user_log.user_id) as user_name", $where, ADMIN_SORT_PATERN[$sort_num][0], ["offset"=>$from,"row_count"=>$num]);
        if($res){
            return $res->fetch_all(MYSQLI_ASSOC);
        }else{
            return null;
        }
    }
    # ユーザーログ件数取得
    public function getUserLogNum($user_id=null,$action="",$detail="",$s=2){
        $where = [];
        if($user_id!==null){
            $where[] = ["user_id","=",$user_id];
        }
        if($action!==""){
            $where[] = ["action","LIKE","%".$action."%"];
        }
        if($detail!==""){
            $where[] = ["detail","LIKE","%".$detail."%"];
        }
        if($s===0){
            $where[] = ["status","=",0];
        }else if($s===1){
            $where[] = ["status","=",1];
        }
        $res = $this->fetch("user_log", "COUNT(*) as num", $where);
        if($res){
            return $res->fetch_array(MYSQLI_ASSOC)['num'];
        }else{
            return null;
        }
    }

    ####################################################################################################

}
