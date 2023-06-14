<?php

class AdminAuth{

    private $admin_data = null;
    private $session_id = null;
    private $pass_admin_data = null;
    private $pass_session_id = null;
    private $login_err = "";
    private $regist_err = "";
    private $forgot_err = "";
    private $reset_err = "";
    private $login_failure_count = 0;

	public function __construct() {
	}
    public function __destruct() {
    }

    
    # クラス変数へのアクセス
    public function admin_data() {
        return $this->admin_data;
    }
    public function session_id() {
        return $this->session_id;
    }
    public function pass_admin_data() {
        return $this->pass_admin_data;
    }
    public function pass_session_id() {
        return $this->pass_session_id;
    }
    public function login_err() {
        return $this->login_err;
    }
    public function forgot_err() {
        return $this->forgot_err;
    }
    public function reset_err() {
        return $this->reset_err;
    }
    public function login_failure_count() {
        return $this->login_failure_count;
    }

    # session_id発行
    public function makeSessId($db){
		for( $i = 0; $i < 10; $i++ ) {
			$session_id = makeRandomId(64);
			if( $db->isExistAdminSessId($session_id) === false ) {
				return $session_id;
			}
		}
        return false;
    }
    public function makePassSessId($db){
		for( $i = 0; $i < 10; $i++ ) {
			$session_id = makeRandomId(64);
			if( $db->isExistAdminPassSessId($session_id) === false ) {
				return $session_id;
			}
		}
        return false;
    }


    ####################################################################################################
    # セッションの認証
    ####################################################################################################
    public function authSession($db, $transaction=true){

        # ログインセッションの確認
        $session_id = ( isset($_COOKIE[COOKIE_NAME_ADMIN_LOGIN]) && $_COOKIE[COOKIE_NAME_ADMIN_LOGIN] !== "" ) ? $_COOKIE[COOKIE_NAME_ADMIN_LOGIN] : null;
        if($session_id===null){
            return false;
        }

        # 期限切れセッションの削除
        $db->cleanAdminSession();

        if($transaction) $db->begin();

        # DBからセッションを取得
        if(!$session = $db->getAdminSession($session_id)){
            if($transaction) $db->rollback();
            return false;
        }

        # 最終アクセス日時を更新
        if(!$db->updateAdminLastAccess($session['admin_id'])){
            if($transaction) $db->rollback();
            return false;
        }

        # 管理者情報の取得
        if(!$admin_data = $db->getAdminDataById($session['admin_id'])){
            if($transaction) $db->rollback();
            return false;
        }

        # ステータス確認
        if((int)$admin_data['status']===1){
            if($transaction) $db->rollback();
            return false;
        }
        # 削除確認
        if((int)$admin_data['delf']===1){
            if($transaction) $db->rollback();
            return false;
        }

        # セッションIDの再交付と有効期限の延長
        $time = time();
        $new_session_id = $this->makeSessId($db);
        if(!$db->extensionAdminSession($time, $session_id, $new_session_id)){
            insertErrorLog($db,500,"500 Internal Server Error","[管理画面]サーバーエラー発生。認証に失敗しています。サーバー不具合またはセッションIDの生成で重複が続いた可能性があります。");
            if($transaction) $db->rollback();
            return false;
        }
        $session_id = $new_session_id;
        $session = $db->getAdminSession($session_id);

        # cookieセット
		setcookie(COOKIE_NAME_ADMIN_LOGIN, $session_id, $time+LOGIN_ADMIN_SESSION_TIME, "/");

        $this->admin_data = $admin_data;
        $this->session_id = $session_id;
        if($transaction) $db->commit();
        return true;
    }


    ####################################################################################################
    # ログイン(validationを除く)
    ####################################################################################################
    public function loginAdmin($db, $admin_email, $admin_password, $transaction=true){
        # 期限切れセッションの削除
        $db->cleanAdminSession();

        if($transaction) $db->begin();
        # メールアドレス照合
        if(!$admin_data = $db->getAdminDataByEmail($admin_email)){
            $this->login_err = "指定されたメールアドレスかパスワードのどちらか、もしくは両方が間違っています。";
            if($transaction) $db->rollback();
            return false;
        }

        # パスワード照合
        if(!verifyPassword($admin_password, $admin_data['admin_password'])){
            $this->login_err = "指定されたメールアドレスかパスワードのどちらか、もしくは両方が間違っています。";
            if($transaction) $db->rollback();
            return false;
        }

        # 最終ログイン日時を更新
        if(!$db->updateAdminLastLogin($admin_data['admin_id'])){
            if($transaction) $db->rollback();
            return false;
        }

        # 管理者情報の取得
        if(!$admin_data = $db->getAdminDataById($admin_data['admin_id'])){
            if($transaction) $db->rollback();
            return false;
        }

        # ステータス確認
        if((int)$admin_data['status']===1){
            $this->login_err = "ログインが規制されています。";
            if($transaction) $db->rollback();
            return false;
        }
        # 削除確認
        if((int)$admin_data['delf']===1){
            $this->login_err = "アカウントが凍結されています。";
            if($transaction) $db->rollback();
            return false;
        }

        # ログインセッションの登録
        $time = time();
        $session_id = $this->makeSessId($db);
        if(!$db->insertAdminSession($time, $admin_data['admin_id'], $session_id)){
            $this->login_err = "ログインエラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","[管理画面]サーバーエラー発生。ログインに失敗しています。サーバー不具合またはセッションIDの生成で重複が続いた可能性があります。error_message：".$this->login_err);
            if($transaction) $db->rollback();
            return false;
        }

        # cookieのセット
		setcookie(COOKIE_NAME_ADMIN_LOGIN, $session_id, $time+LOGIN_ADMIN_SESSION_TIME, "/");
        
        $this->admin_data = $admin_data;
        $this->session_id = $session_id;
        if($transaction) $db->commit();
        return true;
    }


    ####################################################################################################
    # ログアウト
    ####################################################################################################
    public function logoutAdmin($db){
        # ログインセッションの削除
        if(!$db->killAdminSessionDataById($this->admin_data()['admin_id'])){
            insertErrorLog($db,500,"500 Internal Server Error","[管理画面]サーバーエラー発生。ログアウトに失敗しています。");
            return false;
        }

        # セッション変数削除
        $_SESSION = array();

        # cookie削除
        setcookie(COOKIE_NAME_ADMIN_LOGIN, "", time()-3600, "/");

        return true;
    }


    ####################################################################################################
    # 新規登録(validationを除く)
    ####################################################################################################

//     public function registAdmin($db, $admin_name, $admin_email, $admin_password, $transaction=true){
//         if($transaction) $db->begin();

//         # 管理者取得（→validationで行う）
//         if($db->getAdminDataByEmail($admin_email)){
//             $this->regist_err = "指定されたメールアドレスはすでに使用されています。";
//             if($transaction) $db->rollback();
//             return false;
//         }

//         # 登録
//         if(!$admin_id = $db->insertAdminData($admin_name, $admin_email, $admin_password)){
//             $this->regist_err = "登録エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
//             if($transaction) $db->rollback();
//             return false;
//         }

//         # メール送信
//         $site_name = SITE_NAME;
//         $from = FROM_EMAIL;
//         $to = $admin_email;
//         $subject = "{$site_name} 管理者登録完了";
//         $fromName = "{$site_name}";
//         $url = HTTP_ADMIN."/admin/login/";
//         $copyright = COPYRIGHT_EMAIL;
//         $body = <<< EOS
// {$admin_name} 様

// この度は{$site_name}に登録いただき、誠にありがとうございます。
// 以下のURLにアクセスいただき、
// ご登録いただきましたメールアドレスとパスワードにてログインしてご利用ください。

// {$url}

// ※ 本メールは{$site_name}のアカウントやサービスの重要な変更についてお知らせするためにお送りしています。
// ※ お心当たりがない場合は、誠に恐れ入りますが、破棄していただけますようお願いいたします。

// ※ 本メールは{$site_name}より自動でお送りしています。
// ※ 送信専用のアドレスから送信しているため、送信元のアドレス宛にご返信いただいても受信できません。予めご了承ください。
// ※ お問い合わせは、{$site_name}の問い合わせフォームよりお願いいたします。

// {$copyright}

// EOS;
    
//         if(!send_mail($to, $subject, $body, $from, $fromName)){
//             $this->regist_err = "登録エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
//             insertErrorLog($db,500,"500 Internal Server Error","メール送信エラー発生。新規登録のメール送信に失敗しています。error_message：".$this->regist_err);
//             if($transaction) $db->rollback();
//             return false;
//         }

//         if($transaction) $db->commit();
//         return $admin;
//     }


    ####################################################################################################
    # 更新(validationを除く)
    ####################################################################################################

//     public function updateAdmin($db, $admin_id, $admin_name, $admin_email, $admin_password, $status, $admin_position, $transaction=true){
//         if($transaction) $db->begin();

//         # 名前更新
//         if(!$db->updateAdminName($admin_id, $admin_name)){
//             $this->regist_err = "更新エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
//             if($transaction) $db->rollback();
//             return false;
//         }
//         # メールアドレス更新
//         if(!$db->updateAdminEmail($admin_id, $admin_email)){
//             $this->regist_err = "更新エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
//             if($transaction) $db->rollback();
//             return false;
//         }
//         # パスワード更新
//         if(isValid($admin_password) && !$db->updateAdminPassword($admin_id, $admin_password)){
//             $this->regist_err = "更新エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
//             if($transaction) $db->rollback();
//             return false;
//         }
//         # ステータス更新
//         if(isValid($status) && !$db->updateAdminStatus($admin_id, $status)){
//             $this->regist_err = "更新エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
//             if($transaction) $db->rollback();
//             return false;
//         }
//         # ポジション更新
//         if(isValid($admin_position) && !$db->updateAdminPosition($admin_id, $admin_position)){
//             $this->regist_err = "更新エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
//             if($transaction) $db->rollback();
//             return false;
//         }

//         # メール送信
//         $site_name = SITE_NAME;
//         $from = FROM_EMAIL;
//         $to = $admin_email;
//         $subject = "{$site_name} 管理者情報更新完了";
//         $fromName = "{$site_name}";
//         $copyright = COPYRIGHT_EMAIL;
//         $body = <<< EOS
// {$admin_name} 様

// この度は{$site_name}に登録いただき、誠にありがとうございます。
// 管理者情報が更新されました。


// ※ 本メールは{$site_name}のアカウントやサービスの重要な変更についてお知らせするためにお送りしています。
// ※ お心当たりがない場合は、誠に恐れ入りますが、破棄していただけますようお願いいたします。

// ※ 本メールは{$site_name}より自動でお送りしています。
// ※ 送信専用のアドレスから送信しているため、送信元のアドレス宛にご返信いただいても受信できません。予めご了承ください。
// ※ お問い合わせは、{$site_name}の問い合わせフォームよりお願いいたします。

// {$copyright}

// EOS;

//         if(!send_mail($to, $subject, $body, $from, $fromName)){
//             $this->regist_err = "登録エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
//             insertErrorLog($db,500,"500 Internal Server Error","メール送信エラー発生。新規登録のメール送信に失敗しています。error_message：".$this->regist_err);
//             if($transaction) $db->rollback();
//             return false;
//         }

//         if($transaction) $db->commit();
//         return true;
//     }



    ####################################################################################################
    # パスワードセッションの認証
    ####################################################################################################
    public function authPasswordSession($db, $session_id, $transaction=true){
        # 期限切れパスワードセッションの削除
        $db->cleanAdminPasswordSession();

        if($transaction) $db->begin();

        # パスワードセッション取得
        if(!$password_session = $db->getAdminPasswordSession($session_id)){
            if($transaction) $db->rollback();
            return false;
        }

        # 管理者取得
        if(!$admin_data = $db->getAdminDataById($password_session['admin_id'])){
            if($transaction) $db->rollback();
            return false;
        }

        $this->pass_admin_data = $admin_data;
        $this->pass_session_id = $session_id;
        if($transaction) $db->commit();
        return true;
    }


    ####################################################################################################
    # パスワード忘れ
    ####################################################################################################
//     public function forgotAdminPassword($db, $forgot_email, $transaction=true){
//         if($transaction) $db->begin();

//         # 登録されているメールアドレスか確認
//         if(!$admin_data = $db->getAdminDataByEmail($forgot_email)){
//             $this->forgot_err = "このメールアドレスは登録されていません。";
//             if($transaction) $db->rollback();
//             return false;
//         }

//         # セッションを挿入
//         $time = time();
//         $session_id = $this->makePassSessId($db);
//         if(!$db->replaceAdminPasswordSession($time, $admin_data['admin_id'], $session_id)){
//             $this->forgot_err = "パスワード忘れ申請エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
//             insertErrorLog($db,500,"500 Internal Server Error","[管理画面]サーバーエラー発生。パスワード忘れ申請に失敗しています。サーバー不具合またはセッションIDの生成で重複が続いた可能性があります。error_message：".$this->forgot_err);
//             if($transaction) $db->rollback();
//             return false;
//         }

//         # メール送信
//         $site_name = SITE_NAME;
//         $admin_name = $admin_data['admin_name'];
//         global $admin_access;
//         $url = HTTP_ADMIN.$admin_access.'/reset'.'/'.$session_id.'/';
//         error_log($url);
//         $time_limit = date("Y年m月d日 H時i分", $time+FORGOT_ADMIN_SESSION_TIME);
//         $from = FROM_EMAIL;
//         $to = $forgot_email;
//         $subject = "{$site_name} パスワード変更URL送信";
//         $fromName = "{$site_name}";
//         $copyright = COPYRIGHT_EMAIL;
//         $body = <<< EOS
// {$admin_name} 様

// {$site_name}をご利用いただき、誠にありがとうございます。
// 以下のURLにアクセスいただき、
// 有効期限までに新しいパスワードを設定してください。

// {$url}
// 有効期限：{$time_limit}

// ※ 本メールは{$site_name}のアカウントやサービスの重要な変更についてお知らせするためにお送りしています。
// ※ お心当たりがない場合は、誠に恐れ入りますが、破棄していただけますようお願いいたします。

// ※ 本メールは{$site_name}より自動でお送りしています。
// ※ 送信専用のアドレスから送信しているため、送信元のアドレス宛にご返信いただいても受信できません。予めご了承ください。
// ※ お問い合わせは、{$site_name}の問い合わせフォームよりお願いいたします。

// {$copyright}

// EOS;
    
//         if(!send_mail($to, $subject, $body, $from, $fromName)){
//             $this->forgot_err = "パスワード忘れ申請エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
//             insertErrorLog($db,500,"500 Internal Server Error","[管理画面]メール送信エラー発生。パスワード変更のメール送信に失敗しています。error_message：".$this->forgot_err);
//             if($transaction) $db->rollback();
//             return false;
//         }

//         $this->pass_session_id = $session_id;
//         if($transaction) $db->commit();
//         return true;
//     }


    ####################################################################################################
    # パスワードリセット
    ####################################################################################################
//     public function resetAdminPassword($db, $new_password, $transaction=true){
//         if($transaction) $db->begin();

//         $admin_id = $this->pass_admin_data()['admin_id'];
//         # パスワード変更処理
//         if(!$db->changeAdminPassword($admin_id, $new_password)){
//             $this->reset_err = "パスワード変更エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
//             insertErrorLog($db,500,"500 Internal Server Error","[管理画面]サーバーエラー発生。パスワード変更に失敗しています。error_message：".$this->reset_err);
//             if($transaction) $db->rollback();
//             return false;
//         }

//         # パスワードセッション削除
//         if(!$db->killAdminPasswordSessionDataById($admin_id)){
//             $this->reset_err = "パスワード変更エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
//             insertErrorLog($db,500,"500 Internal Server Error","[管理画面]サーバーエラー発生。パスワード変更に失敗しています。error_message：".$this->reset_err);
//             if($transaction) $db->rollback();
//             return false;
//         }

//         # メール送信
//         $site_name = SITE_NAME;
//         $admin = $this->pass_admin_data();
//         $admin_name = $admin['admin_name'];
//         $admin_email = $admin['admin_email'];
//         $from = FROM_EMAIL;
//         $to = $admin['admin_email'];
//         $subject = "{$site_name} パスワード変更完了";
//         $fromName = "{$site_name}";
//         global $admin_access;
//         $url = HTTP_ADMIN.$admin_access."/"."login/";
//         $copyright = COPYRIGHT_EMAIL;
//         $body = <<< EOS
// {$admin_name} 様

// {$site_name}をご利用いただき、誠にありがとうございます。
// パスワードの再設定が完了いたしました。
// 以下のURLにアクセスいただき、
// ご登録いただきましたメールアドレスとパスワードにてログインしてご利用ください。

// {$url}

// ※ 本メールは{$site_name}のアカウントやサービスの重要な変更についてお知らせするためにお送りしています。
// ※ お心当たりがない場合は、誠に恐れ入りますが、破棄していただけますようお願いいたします。

// ※ 本メールは{$site_name}より自動でお送りしています。
// ※ 送信専用のアドレスから送信しているため、送信元のアドレス宛にご返信いただいても受信できません。予めご了承ください。
// ※ お問い合わせは、{$site_name}の問い合わせフォームよりお願いいたします。

// {$copyright}

// EOS;
    
//         if(!send_mail($to, $subject, $body, $from, $fromName)){
//             $this->reset_err = "パスワード変更エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
//             insertErrorLog($db,500,"500 Internal Server Error","[管理画面]メール送信エラー発生。パスワード変更のメール送信に失敗しています。error_message：".$this->reset_err);
//             if($transaction) $db->rollback();
//             return false;
//         }

//         $this->pass_admin_data = null;
//         $this->pass_session_id = null;
//         if($transaction) $db->commit();
//         return true;
//     }


}
