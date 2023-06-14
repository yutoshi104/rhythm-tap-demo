<?php

trait AdminDb {

    ####################################################################################################
    # adminテーブル
    ####################################################################################################

	# 管理者取得(by id)
	public function getAdminDataById($admin_id) {
		$res = $this->fetch("admin", "*", [["admin_id","=",$admin_id]]);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC);
		}else{
			return null;
		}
	}
    # 管理者取得(by email)
	public function getAdminDataByEmail($admin_email) {
		$res = $this->fetch("admin", "*", [["admin_email","=",$admin_email]]);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC);
		}else{
			return null;
		}
        return $res->fetch_array(MYSQLI_ASSOC);
	}
    # 管理者取得(for 管理者管理)
	public function getAdminDataSearch($admin_name="",$admin_email="",$d=0,$sort_num=0,$from=0,$num=ADMIN_PAGING_CHECK) {
		$where = [];
        if($admin_name!==""){
			$where[] = ["admin_name","LIKE","%".$admin_name."%"];
        }
        if($admin_email!==""){
			$where[] = ["admin_email","LIKE","%".$admin_email."%"];
        }
		if(!$d){
			$where[] = ["delf","=",0];
		}
		$res = $this->fetch("admin", "*", $where, ADMIN_SORT_PATERN[$sort_num][0], ["offset"=>$from,"row_count"=>$num]);
		if($res){
			return $res->fetch_all(MYSQLI_ASSOC);
		}else{
			return null;
		}
	}
    # 管理者数取得(for 管理者管理)
	public function getAdminDataNumSearch($admin_name="",$admin_email="",$d=0) {
		$where = [];
        if($admin_name!==""){
			$where[] = ["admin_name","LIKE","%".$admin_name."%"];
        }
        if($admin_email!==""){
			$where[] = ["admin_email","LIKE","%".$admin_email."%"];
        }
		if(!$d){
			$where[] = ["delf","=",0];
		}
		$res = $this->fetch("admin", "COUNT(*) as num", $where);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC)['num'];
		}else{
			return null;
		}
	}
    # 管理者登録
    public function insertAdminData($admin_name, $admin_email, $admin_password, $admin_position, $status){
        $admin_password = encryptPassword($admin_password);
        return $this->insert("admin", ["admin_name"=>$admin_name,"admin_email"=>$admin_email,"admin_password"=>$admin_password,"admin_position"=>$admin_position,"status"=>$status], true, true);
    }
    # 管理者更新(admin_name)
    public function updateAdminName($admin_id, $admin_name){
        return $this->update("admin", ["admin_name"=>$admin_name], true, [["admin_id","=",$admin_id]]);
    }
    # 管理者更新(admin_email)
    public function updateAdminEmail($admin_id, $admin_email){
        return $this->update("admin", ["admin_email"=>$admin_email], true, [["admin_id","=",$admin_id]]);
    }
    # 管理者更新(admin_password)
    public function updateAdminPassword($admin_id, $admin_password){
        $admin_password = encryptPassword($admin_password);
        return $this->update("admin", ["admin_password"=>$admin_password], true, [["admin_id","=",$admin_id]]);
    }
    # 管理者更新(status)
    public function updateAdminStatus($admin_id, $status){
        return $this->update("admin", ["status"=>$status], true, [["admin_id","=",$admin_id]]);
    }
    # 管理者更新(position)
    public function updateAdminPosition($admin_id, $admin_position){
        return $this->update("admin", ["admin_position"=>$admin_position], true, [["admin_id","=",$admin_id]]);
    }
    # 管理者更新(last_login)
    public function updateAdminLastLogin($admin_id){
        return $this->update("admin", ["last_login"=>getTime()], false, [["admin_id","=",$admin_id]]);
    }
    # 管理者更新(last_access)
    public function updateAdminLastAccess($admin_id){
        return $this->update("admin", ["last_access"=>getTime()], false, [["admin_id","=",$admin_id]]);
    }
    # 管理者削除
    public function deleteAdminData($admin_id){
        return $this->logicalDelete("admin", true, [["admin_id","=",$admin_id]]);
    }
    # 管理者復元(from email)
    public function restoreAdminData($admin_email){
        return $this->update("admin", ["delf"=>0], true, [["admin_email","=",$admin_email]]);
    }
    # 管理者ログイン規制解除
    public function changeAdminStatusNormal($admin_id){
        return $this->update("admin", ["status"=>0], true, [["admin_id","=",$admin_id]]);
    }
    # 管理者ログイン規制
    public function changeAdminStatusReg($admin_id){
        return $this->update("admin", ["status"=>1], true, [["admin_id","=",$admin_id]]);
    }
    # 管理者パスワード変更
    public function changeAdminPassword($admin_id, $new_password){
        $new_password = encryptPassword($new_password);
        return $this->update("admin", ["admin_password"=>$new_password], true, [["admin_id","=",$admin_id]]);
    }

    ####################################################################################################


    ####################################################################################################
    # admin_sessionテーブル
    ####################################################################################################

    # ログインセッション取得(by session_id)
    public function getAdminSession($session_id){
		$res = $this->fetch("admin_session", "*", [["session_id","=",$session_id]]);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC);
		}else{
			return null;
		}
    }
    # ログインセッション登録
    public function insertAdminSession($now_time, $admin_id, $session_id){
        $params = [
            "admin_id" => $admin_id,
            "session_id" => $session_id,
            "ip" => $_SERVER["REMOTE_ADDR"],
            "ua" => $_SERVER['HTTP_USER_AGENT'],
            "deltime" => date("Y-m-d H:i:s", $now_time+LOGIN_ADMIN_SESSION_TIME),
        ];
        return $this->insert("admin_session", $params, true, true);
    }
    # ログインセッションIDの再交付と有効期限延長
    public function extensionAdminSession($now_time, $past_session_id, $session_id){
		$params = [
            "session_id" => $session_id,
			"ip" => $_SERVER["REMOTE_ADDR"],
			"ua" => $_SERVER['HTTP_USER_AGENT'],
			"deltime" => date("Y-m-d H:i:s", $now_time+LOGIN_ADMIN_SESSION_TIME),
		];
        $res = $this->update("admin_session", $params, true, [["session_id","=",$past_session_id]]);
        return $res;
    }
    # ログインセッションIDが存在するか
    public function isExistAdminSessId($session_id){
        return $this->isExistData("admin_session", [["session_id","=",$session_id]]);
    }
    # ログインセッション削除(by id)
    public function killAdminSessionDataById($admin_id){
		return $this->physicalDelete("admin_session", [["admin_id","=",$admin_id]]);
    }
    # ログインセッション削除(by session_id)
    public function killAdminSessionDataBySessId($session_id){
		return $this->physicalDelete("admin_session", [["session_id","=",$session_id]]);
    }
    # 期限切れのログインセッション削除
    public function cleanAdminSession(){
        $now = getTime();
        return $this->physicalDelete("admin_session", [["deltime","<=",$now]]);
    }

    ####################################################################################################

    ####################################################################################################
    # admin_password_sessionテーブル
    ####################################################################################################

    # パスワードセッション取得(by session_id)
    public function getAdminPasswordSession($session_id){
		$res = $this->fetch("admin_password_session", "*", [["session_id","=",$session_id]]);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC);
		}else{
			return null;
		}
    }
    # パスワードセッション取得(by admin_id)
    public function getAdminPasswordSessionByAdminId($admin_id){
        $res = $this->fetch("admin_password_session", "*", [["admin_id","=",$admin_id]]);
        if($res){
            return $res->fetch_array(MYSQLI_ASSOC);
        }else{
            return null;
        }
    }
    # パスワードセッション登録
    public function replaceAdminPasswordSession($now_time, $admin_id, $session_id){
        $params = [
            "admin_id" => $admin_id,
            "session_id" => $session_id,
            "ip" => $_SERVER["REMOTE_ADDR"],
            "ua" => $_SERVER['HTTP_USER_AGENT'],
            "deltime" => date("Y-m-d H:i:s", $now_time+FORGOT_ADMIN_SESSION_TIME),
        ];
        return $this->replace("admin_password_session", $params, true, false);
    }
    # パスワードセッションIDが存在するか
    public function isExistAdminPassSessId($session_id){
        return $this->isExistData("admin_password_session", [["session_id","=",$session_id]]);
    }
    # パスワードセッション削除(by id)
    public function killAdminPasswordSessionDataById($admin_id){
		return $this->physicalDelete("admin_password_session", [["admin_id","=",$admin_id]]);
    }
    # パスワードセッション削除(by session_id)
    public function killAdminPasswordSessionDataBySessId($session_id){
		return $this->physicalDelete("admin_password_session", [["session_id","=",$session_id]]);
    }
    # 期限切れのパスワードセッション削除
    public function cleanAdminPasswordSession(){
        $now = getTime();
        return $this->physicalDelete("admin_password_session", [["deltime","<=",$now]]);
    }

    ####################################################################################################


}
