<?php

trait UserDb {

    ####################################################################################################
    # userテーブル
    ####################################################################################################

	# ユーザー取得(by id)
	public function getUserDataById($user_id) {
		$res = $this->fetch("user", "*", [["user_id","=",$user_id]]);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC);
		}else{
			return null;
		}
	}
    # ユーザー取得(by email)
	public function getUserDataByEmail($user_email) {
		$res = $this->fetch("user", "*", [["user_email","=",$user_email]]);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC);
		}else{
			return null;
		}
	}
    # ユーザー登録
    public function insertUserData($user_name, $user_email, $user_password, $user_position){
        $user_password = encryptPassword($user_password);
        return $this->insert("user", ["user_name"=>$user_name,"user_email"=>$user_email,"user_password"=>$user_password,"user_position"=>$user_position], true, true);
    }
    # ユーザー更新(user_name)
    public function updateUserName($user_id, $user_name){
        return $this->update("user", ["user_name"=>$user_name], true, [["user_id","=",$user_id]]);
    }
    # ユーザー更新(user_email)
    public function updateUserEmail($user_id, $user_email){
        return $this->update("user", ["user_email"=>$user_email], true, [["user_id","=",$user_id]]);
    }
    # ユーザー更新(user_password)
    public function updateUserPassword($user_id, $user_password){
        $user_password = encryptPassword($user_password);
        return $this->update("user", ["user_password"=>$user_password], true, [["user_id","=",$user_id]]);
    }
    # ユーザー更新(user_position)
    public function updateUserPosition($user_id, $user_position){
        return $this->update("user", ["user_position"=>$user_position], true, [["user_id","=",$user_id]]);
    }
    # ユーザー更新(last_login)
    public function updateUserLastLogin($user_id){
        return $this->update("user", ["last_login"=>getTime()], false, [["user_id","=",$user_id]]);
    }
    # ユーザー更新(last_access)
    public function updateUserLastAccess($user_id){
        return $this->update("user", ["last_access"=>getTime()], false, [["user_id","=",$user_id]]);
    }
    # ユーザー削除
    public function deleteUserData($user_id){
        return $this->logicalDelete("user", true, [["user_id","=",$user_id]]);
    }
    # ユーザー復元(from email)
    public function restoreUserData($user_email){
        return $this->update("user", ["delf"=>0], true, [["user_email","=",$user_email]]);
    }
    # ユーザーログイン規制解除
    public function changeUserStatusNormal($user_id){
        return $this->update("user", ["status"=>0], true, [["user_id","=",$user_id]]);
    }
    # ユーザーログイン規制
    public function changeUserStatusReg($user_id){
        return $this->update("user", ["status"=>1], true, [["user_id","=",$user_id]]);
    }
    # ユーザーパスワード変更
    public function changeUserPassword($user_id, $new_password){
        $new_password = encryptPassword($new_password);
        return $this->update("user", ["user_password"=>$new_password], true, [["user_id","=",$user_id]]);
    }

    ####################################################################################################


    ####################################################################################################
    # user_detailテーブル
    ####################################################################################################

	# ユーザー詳細取得(by user_id)
	public function getUserDetailDataById($user_id) {
		$res = $this->fetch("user_detail", "*", [["user_id","=",$user_id]]);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC);
		}else{
			return null;
		}
	}
    # ユーザー詳細取得(by user_key)
	public function getUserDetailDataByKey($user_key) {
		$res = $this->fetch("user_detail", "*", [["user_key","=",$user_key]]);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC);
		}else{
			return null;
		}
	}

    # ユーザー詳細登録
    public function insertUserDetailData($user_id, $user_details){
        $user_details['user_id'] = (int)$user_id;
        return $this->insert("user_detail", $user_details, false, false);
    }
    # ユーザー詳細更新
    public function updateUserDetail($user_id, $user_details){
        return $this->update("user_detail", $user_details, false, [["user_id","=",$user_id]]);
    }
    // # ユーザー詳細更新(user_key)
    // public function updateUserDetailKey($user_id, $user_key){
    //     return $this->update("user_detail", ["user_key"=>$user_key], false, [["user_id","=",$user_id]]);
    // }
    // # ユーザー詳細更新(user_nickname)
    // public function updateUserDetailNickName($user_id, $user_nickname){
    //     return $this->update("user_detail", ["user_nickname"=>$user_nickname], false, [["user_id","=",$user_id]]);
    // }
    // # ユーザー詳細更新(guest_flg)
    // public function updateUserDetailGuestFlg($user_id, $guest_flg){
    //     return $this->update("user_detail", ["guest_flg"=>$guest_flg], false, [["user_id","=",$user_id]]);
    // }

    ####################################################################################################





    ####################################################################################################
    # user,user_detailテーブル
    ####################################################################################################

    # ユーザー&ユーザー詳細取得(by user_id)
	public function getUserUserDetailDataById($user_id) {
		$res1 = $this->fetch("user", "*", [["user_id","=",$user_id]]);
		if($res1){
            $res1 = $res1->fetch_array(MYSQLI_ASSOC);
            $ui = $res1['user_id'];
            $res2 = $this->fetch("user_detail", "*", [["user_id","=",$ui]]);
            if($res2){
                $res2 = $res2->fetch_array(MYSQLI_ASSOC);
                return array_merge($res1,$res2);
            }else{
                return null;
            }
		}else{
			return null;
		}
	}
    # ユーザー&ユーザー詳細取得(by user_id)(not delf)
	public function getUserUserDetailDataByIdNotDelf($user_id) {
		$res1 = $this->fetch("user", "*", [["user_id","=",$user_id],["delf","=",0]]);
		if($res1){
            $res1 = $res1->fetch_array(MYSQLI_ASSOC);
            $ui = $res1['user_id'];
            $res2 = $this->fetch("user_detail", "*", [["user_id","=",$ui]]);
            if($res2){
                $res2 = $res2->fetch_array(MYSQLI_ASSOC);
                return array_merge($res1,$res2);
            }else{
                return null;
            }
		}else{
			return null;
		}
	}
    # ユーザー&ユーザー詳細取得(by user_key)
	public function getUserUserDetailDataByKey($user_key) {
		$res1 = $this->fetch("user_detail", "*", [["user_key","=",$user_key]]);
		if($res1){
            $res1 = $res1->fetch_array(MYSQLI_ASSOC);
            $ui = $res1['user_id'];
            $res2 = $this->fetch("user", "*", [["user_id","=",$ui]]);
            if($res2){
                $res2 = $res2->fetch_array(MYSQLI_ASSOC);
                return array_merge($res1,$res2);
            }else{
                return null;
            }
		}else{
			return null;
		}
	}
    # ユーザー&ユーザー詳細取得(by user_key)(not delf)
	public function getUserUserDetailDataByKeyNotDelf($user_key) {
		$res1 = $this->fetch("user_detail", "*", [["user_key","=",$user_key]]);
		if($res1){
            $res1 = $res1->fetch_array(MYSQLI_ASSOC);
            $ui = $res1['user_id'];
            $res2 = $this->fetch("user", "*", [["user_id","=",$ui],["delf","=",0]]);
            if($res2){
                $res2 = $res2->fetch_array(MYSQLI_ASSOC);
                return array_merge($res1,$res2);
            }else{
                    return null;
            }
		}else{
			return null;
		}
	}

    ####################################################################################################




    ####################################################################################################
    # user,user_detailテーブル
    ####################################################################################################

    # ユーザー取得
	public function getUserUserDetailByUserId($user_id) {
        $sql = sprintf("SELECT user.*,user_detail.* FROM user INNER JOIN user_detail ON user.user_id = user_detail.user_id WHERE user.user_id='%s'", $this->escape($user_id));
        $res = $this->query($sql);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC);
		}else{
			return null;
		}
	}
    # ユーザー取得(for 検索)
	public function getUserUserDetailSearch($user_nickname="",$user_email="",$d=0,$sort_num=0,$from=0,$num=ADMIN_PAGING_CHECK) {
		if(!$d){
			$where = " AND `delf`=0";
		}else{
            $where = "";
        }
        $sql = sprintf("SELECT user.*,user_detail.* FROM user INNER JOIN user_detail ON user.user_id = user_detail.user_id WHERE (`user_nickname` LIKE '%%%s%%') AND (`user_email` LIKE '%%%s%%')%s ORDER BY %s LIMIT %d,%d", $this->escape($user_nickname), $this->escape($user_email), $where, $this->escape(implode(" ",ADMIN_SORT_PATERN[$sort_num][0])), $this->escape($from), $this->escape($num));
        $res = $this->query($sql);
		if($res){
			return $res->fetch_all(MYSQLI_ASSOC);
		}else{
			return null;
		}
	}
    # ユーザー数取得(for 検索)
	public function getUserUserDetailNumSearch($user_nickname="",$user_email="",$d=0) {
		if(!$d){
			$where = " AND `delf`=0";
		}else{
            $where = "";
        }
        $sql = sprintf("SELECT COUNT(*) as num FROM user INNER JOIN user_detail ON user.user_id = user_detail.user_id WHERE (`user_nickname` LIKE '%%%s%%') AND (`user_email` LIKE '%%%s%%')%s", $this->escape($user_nickname), $this->escape($user_email), $where);
        $res = $this->query($sql);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC)['num'];
		}else{
			return null;
		}
	}

    ####################################################################################################


    ####################################################################################################
    # user_sessionテーブル
    ####################################################################################################

    # ログインセッション取得(by session_id)
    public function getUserSession($session_id){
		$res = $this->fetch("user_session", "*", [["session_id","=",$session_id]]);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC);
		}else{
			return null;
		}
    }
    # ログインセッション登録
    public function insertUserSession($now_time, $user_id, $session_id){
        $params = [
            "user_id" => $user_id,
            "session_id" => $session_id,
            "ip" => $_SERVER["REMOTE_ADDR"],
            "ua" => $_SERVER['HTTP_USER_AGENT'],
            "deltime" => date("Y-m-d H:i:s", $now_time+LOGIN_USER_SESSION_TIME),
        ];
        return $this->insert("user_session", $params, true, true);
    }
    # ログインセッションIDの再交付と有効期限延長
    public function extensionUserSession($now_time, $past_session_id, $session_id){
		$params = [
            "session_id" => $session_id,
			"ip" => $_SERVER["REMOTE_ADDR"],
			"ua" => $_SERVER['HTTP_USER_AGENT'],
			"deltime" => date("Y-m-d H:i:s", $now_time+LOGIN_USER_SESSION_TIME),
		];
        $res = $this->update("user_session", $params, true, [["session_id","=",$past_session_id]]);
        return $res;
    }
    # ログインセッションIDが存在するか
    public function isExistUserSessId($session_id){
        return $this->isExistData("user_session", [["session_id","=",$session_id]]);
    }
    # ログインセッション削除(by id)
    public function killUserSessionDataById($user_id){
		return $this->physicalDelete("user_session", [["user_id","=",$user_id]]);
    }
    # ログインセッション削除(by session_id)
    public function killUserSessionDataBySessId($session_id){
		return $this->physicalDelete("user_session", [["session_id","=",$session_id]]);
    }
    # 期限切れのログインセッション削除
    public function cleanUserSession(){
        $now = getTime();
        return $this->physicalDelete("user_session", [["deltime","<=",$now]]);
    }

    ####################################################################################################

    ####################################################################################################
    # user_password_sessionテーブル
    ####################################################################################################

    # パスワードセッション取得(by session_id)
    public function getUserPasswordSession($session_id){
		$res = $this->fetch("user_password_session", "*", [["session_id","=",$session_id]]);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC);
		}else{
			return null;
		}
    }
    # パスワードセッション取得(by user_id)
    public function getUserPasswordSessionByUserId($user_id){
        $res = $this->fetch("user_password_session", "*", [["user_id","=",$user_id]]);
        if($res){
            return $res->fetch_array(MYSQLI_ASSOC);
        }else{
            return null;
        }
    }
    # パスワードセッション登録
    public function replaceUserPasswordSession($now_time, $user_id, $session_id){
        $params = [
            "user_id" => $user_id,
            "session_id" => $session_id,
            "ip" => $_SERVER["REMOTE_ADDR"],
            "ua" => $_SERVER['HTTP_USER_AGENT'],
            "deltime" => date("Y-m-d H:i:s", $now_time+FORGOT_USER_SESSION_TIME),
        ];
        return $this->replace("user_password_session", $params, true, false);
    }
    # パスワードセッションIDが存在するか
    public function isExistUserPassSessId($session_id){
        return $this->isExistData("user_password_session", [["session_id","=",$session_id]]);
    }
    # パスワードセッション削除(by id)
    public function killUserPasswordSessionDataById($user_id){
		return $this->physicalDelete("user_password_session", [["user_id","=",$user_id]]);
    }
    # パスワードセッション削除(by session_id)
    public function killUserPasswordSessionDataBySessId($session_id){
		return $this->physicalDelete("user_password_session", [["session_id","=",$session_id]]);
    }
    # 期限切れのパスワードセッション削除
    public function cleanUserPasswordSession(){
        $now = getTime();
        return $this->physicalDelete("user_password_session", [["deltime","<=",$now]]);
    }

    ####################################################################################################

    ####################################################################################################
    # user_email_sessionテーブル
    ####################################################################################################

    # パスワードセッション取得(by session_id)
    public function getUserEmailSession($session_id){
		$res = $this->fetch("user_email_session", "*", [["session_id","=",$session_id]]);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC);
		}else{
			return null;
		}
    }
    # パスワードセッション取得(by user_id)
    public function getUserEmailSessionByUserId($user_id){
        $res = $this->fetch("user_password_session", "*", [["user_id","=",$user_id]]);
        if($res){
            return $res->fetch_array(MYSQLI_ASSOC);
        }else{
            return null;
        }
    }
    # パスワードセッション登録
    public function replaceUserEmailSession($now_time, $user_email, $session_id){
        $params = [
            "user_email" => $user_email,
            "session_id" => $session_id,
            "ip" => $_SERVER["REMOTE_ADDR"],
            "ua" => $_SERVER['HTTP_USER_AGENT'],
            "deltime" => date("Y-m-d H:i:s", $now_time+EMAIL_USER_SESSION_TIME),
        ];
        return $this->replace("user_email_session", $params, true, false);
    }
    # パスワードセッションIDが存在するか
    public function isExistUserEmailSessId($session_id){
        return $this->isExistData("user_email_session", [["session_id","=",$session_id]]);
    }
    # パスワードセッション削除(by id)
    public function killUserEmailSessionDataById($user_email){
		return $this->physicalDelete("user_email_session", [["user_email","=",$user_email]]);
    }
    # パスワードセッション削除(by session_id)
    public function killUserEmailSessionDataBySessId($session_id){
		return $this->physicalDelete("user_email_session", [["session_id","=",$session_id]]);
    }
    # 期限切れのパスワードセッション削除
    public function cleanUserEmailSession(){
        $now = getTime();
        return $this->physicalDelete("user_email_session", [["deltime","<=",$now]]);
    }

    ####################################################################################################



    ####################################################################################################
    # followテーブル
    ####################################################################################################

    // # フォロー取得(by follow_id)
	// public function getFollowDataByFollowId($follow_id) {
	// 	$res = $this->fetch("follow", "*", [["follow_id","=",$follow_id]]);
	// 	if($res){
	// 		return $res->fetch_array(MYSQLI_ASSOC);
	// 	}else{
	// 		return null;
	// 	}
	// }
    // # フォロー取得(by from_user_id)
	// public function getFollowDataByFromUserId($from_user_id) {
	// 	$res = $this->fetch("follow", "*", [["from_user_id","=",$from_user_id]]);
	// 	if($res){
	// 		return $res->fetch_all(MYSQLI_ASSOC);
	// 	}else{
	// 		return null;
	// 	}
	// }
    // # フォロー取得(by from_user_id)
	// public function getFollowDataNickNameByFromUserId($from_user_id,$user_id) {
    //     $sql = sprintf("SELECT follow.from_user_id,follow.to_user_id,user_detail.user_nickname,(SELECT COUNT(*) FROM follow as f WHERE from_user_id=follow.to_user_id AND to_user_id=follow.from_user_id) as each_follow,(SELECT COUNT(*) FROM follow as f WHERE from_user_id='%s' AND to_user_id=follow.to_user_id) as user_follow FROM follow INNER JOIN user_detail ON follow.to_user_id = user_detail.user_id WHERE `from_user_id`='%s' ORDER BY follow.intime DESC", $this->escape($user_id), $this->escape($from_user_id));
    //     $res = $this->query($sql);
	// 	if($res){
	// 		return $res->fetch_all(MYSQLI_ASSOC);
	// 	}else{
	// 		return null;
	// 	}
	// }
    // # フォロワー取得(by from_user_id)
	// public function getFollowerDataNickNameByFromUserId($to_user_id,$user_id) {
    //     $sql = sprintf("SELECT follow.from_user_id,follow.to_user_id,user_detail.user_nickname,(SELECT COUNT(*) FROM follow as f WHERE from_user_id=follow.to_user_id AND to_user_id=follow.from_user_id) as each_follow,(SELECT COUNT(*) FROM follow as f WHERE from_user_id='%s' AND to_user_id=follow.from_user_id) as user_follow FROM follow INNER JOIN user_detail ON follow.from_user_id = user_detail.user_id WHERE `to_user_id`='%s' ORDER BY follow.intime DESC", $this->escape($user_id), $this->escape($to_user_id));
    //     $res = $this->query($sql);
	// 	if($res){
	// 		return $res->fetch_all(MYSQLI_ASSOC);
	// 	}else{
	// 		return null;
	// 	}
	// }
    // # フォロー取得(by from_user_id)
	// public function getFollowerDataEachFollow($from_user_id,$to_user_id) {
    //     $sql = sprintf("SELECT * FROM follow WHERE `from_user_id`='%s' AND `to_user_id`='%s'", $this->escape($from_user_id), $this->escape($to_user_id));
    //     $res = $this->query($sql);
	// 	if($res){
	// 		return $res->fetch_all(MYSQLI_ASSOC);
	// 	}else{
	// 		return null;
	// 	}
	// }
    // # フォロー数取得(by from_user_id)
	// public function getFollowDataNumByFromUserId($from_user_id) {
	// 	$res = $this->fetch("follow", "COUNT(*) as num", [["from_user_id","=",$from_user_id]]);
	// 	if($res){
	// 		return $res->fetch_array(MYSQLI_ASSOC)['num'];
	// 	}else{
	// 		return null;
	// 	}
	// }
    // # フォロー取得(by to_user_id)
	// public function getFollowDataByToUserId($to_user_id) {
	// 	$res = $this->fetch("follow", "*", [["to_user_id","=",$to_user_id]]);
	// 	if($res){
	// 		return $res->fetch_all(MYSQLI_ASSOC);
	// 	}else{
	// 		return null;
	// 	}
	// }
    // # フォロー数取得(by to_user_id)
	// public function getFollowDataNumByToUserId($to_user_id) {
	// 	$res = $this->fetch("follow", "COUNT(*) as num", [["to_user_id","=",$to_user_id]]);
	// 	if($res){
	// 		return $res->fetch_array(MYSQLI_ASSOC)['num'];
	// 	}else{
	// 		return null;
	// 	}
	// }
    // # フォロー登録
    // public function insertFollowData($from_user_id, $to_user_id){
    //     return $this->insert("follow", ["from_user_id"=>$from_user_id,"to_user_id"=>$to_user_id], true, false);
    // }
    // # フォロー削除
    // public function deleteFollowData($from_user_id, $to_user_id){
    //     return $this->physicalDelete("follow", [["from_user_id","=",$from_user_id],["to_user_id","=",$to_user_id]]);
    // }
    // # フォロー削除
    // public function deleteFollowDataForFollowId($follow_id){
    //     return $this->physicalDelete("follow", [["follow_id","=",$follow_id]]);
    // }
    // # フォロー削除(from_user_id)
    // public function deleteFollowDataForFromUserId($from_user_id){
    //     return $this->physicalDelete("follow", [["from_user_id","=",$from_user_id]]);
    // }
    // # フォロー削除(to_user_id)
    // public function deleteFollowDataForToUserId($to_user_id){
    //     return $this->physicalDelete("follow", [["to_user_id","=",$to_user_id]]);
    // }

    ####################################################################################################


    ####################################################################################################
    # leaveテーブル
    ####################################################################################################

    # 退会取得(from leave_id)
	public function getUserLeaveData($leave_id){
		$res = $this->fetch("user_leave", "*", [["leave_id","=",$leave_id]]);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC);
		}else{
			return null;
		}
	}
	# 退会取得(for check)
	public function getUserLeaveDataCheck($user_id=null,$detail="",$reason=null,$q="all",$sort_num=0,$from=0,$num=ADMIN_PAGING_CHECK) {
		$where = [];
        if($user_id){
			$where[] = ["user_id","=",$user_id];
		}
		if($detail){
			$where[] = ["leave_detail","LIKE","%".$detail."%"];
		}
		if($reason){
			$where[] = ["leave_reason","=",$reason];
		}
		if($q==="nochecked"){
			$where[] = ["checked","=",0];
		}else if($q==="checked"){
			$where[] = ["checked","=",1];
		}
		$res = $this->fetch("user_leave", "*", $where, ADMIN_SORT_PATERN[$sort_num][0], ["offset"=>$from,"row_count"=>$num]);
		if($res){
			return $res->fetch_all(MYSQLI_ASSOC);
		}else{
			return null;
		}
	}
	# 退会件数取得(for check)
	public function getUserLeaveDataNumCheck($user_id="",$detail="",$reason=null,$q="all") {
		$where = [];
        if($user_id){
			$where[] = ["user_id","=",$user_id];
		}
		if($detail){
			$where[] = ["leave_detail","LIKE","%".$detail."%"];
		}
		if($reason){
			$where[] = ["leave_reason","=",$reason];
		}
		if($q==="nochecked"){
			$where[] = ["checked","=",0];
		}else if($q==="checked"){
			$where[] = ["checked","=",1];
		}
		$res = $this->fetch("user_leave", "COUNT(*) as num", $where);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC)['num'];
		}else{
			return null;
		}
	}
	# 退会登録
    public function insertUserLeaveData($user_id,$leave_reason,$leave_detail){
        return $this->insert("user_leave", ["user_id"=>$user_id,"leave_reason"=>$leave_reason,"leave_detail"=>$leave_detail], true, true);
    }
	# 退会チェック済み
	public function updateUserLeaveChecked($leave_id){
		return $this->update("user_leave", ["checked"=>1], true, [["leave_id","=",$leave_id]]);
	}
	# 退会チェック解除
	public function updateUserLeaveNoChecked($leave_id){
		return $this->update("user_leave", ["checked"=>0], true, [["leave_id","=",$leave_id]]);
	}
	# 退会ステータスオン
	public function updateUserLeaveStatusOn($leave_id){
		return $this->update("user_leave", ["status"=>1], true, [["leave_id","=",$leave_id]]);
	}
	# 退会ステータスオフ
	public function updateUserLeaveStatusOff($leave_id){
		return $this->update("user_leave", ["status"=>0], true, [["leave_id","=",$leave_id]]);
	}

    ####################################################################################################

}
