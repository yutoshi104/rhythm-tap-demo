<?php

class UserAuth{

    private $user_data = null;
    private $session_id = null;
    private $email_session_id = null;
    private $pass_user_data = null;
    private $pass_session_id = null;
    private $login_err = "";
    private $regist_err = "";
    private $regist_guest_err = "";
    private $emamil_auth_ere = "";
    private $edit_err = "";
    private $leave_err = "";
    private $forgot_err = "";
    private $reset_err = "";
    private $login_failure_count = 0;

	public function __construct() {
	}
    public function __destruct() {
    }

    
    # クラス変数へのアクセス
    public function user_data() {
        return $this->user_data;
    }
    public function session_id() {
        return $this->session_id;
    }
    public function email_session_id() {
        return $this->email_session_id;
    }
    public function pass_user_data() {
        return $this->pass_user_data;
    }
    public function pass_session_id() {
        return $this->pass_session_id;
    }
    public function login_err() {
        return $this->login_err;
    }
    public function regist_err() {
        return $this->regist_err;
    }
    public function regist_guest_err() {
        return $this->regist_guest_err;
    }
    public function email_auth_err() {
        return $this->email_auth_err;
    }
    public function edit_err() {
        return $this->edit_err;
    }
    public function leave_err() {
        return $this->leave_err;
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

    /* {$site_name} */
    private $user_detail_data = null;
    public function user_detail_data() {
        return $this->user_detail_data;
    }

    # session_id発行
    public function makeSessId($db){
		for( $i = 0; $i < 10; $i++ ) {
			$session_id = makeRandomId(64);
			if( $db->isExistUserSessId($session_id) === false ) {
				return $session_id;
			}
		}
        return false;
    }
    public function makeEmailSessId($db){
		for( $i = 0; $i < 10; $i++ ) {
			$session_id = makeRandomId(64);
			if( $db->isExistUserEmailSessId($session_id) === false ) {
				return $session_id;
			}
		}
        return false;
    }
    public function makePassSessId($db){
		for( $i = 0; $i < 10; $i++ ) {
			$session_id = makeRandomId(64);
			if( $db->isExistUserPassSessId($session_id) === false ) {
				return $session_id;
			}
		}
        return false;
    }

    # ゲストユーザーキー発行
    public function makeGuestUserKey($db){
		for( $i = 0; $i < 10; $i++ ) {
			$key = makeRandomId(16);
			if( $db->getUserDetailDataByKey($key) === null ) {
				return $key;
			}
		}
        return false;
    }

    ####################################################################################################
    # セッションの認証
    ####################################################################################################
    public function authSession($db, $transaction=true){

        # ログインセッションの確認
        $session_id = ( isset($_COOKIE[COOKIE_NAME_USER_LOGIN]) && $_COOKIE[COOKIE_NAME_USER_LOGIN] !== "" ) ? $_COOKIE[COOKIE_NAME_USER_LOGIN] : null;
        if($session_id===null){
            return false;
        }

        # 期限切れセッションの削除
        $db->cleanUserSession();

        if($transaction) $db->begin();

        # DBからセッションを取得
        if(!$session = $db->getUserSession($session_id)){
            if($transaction) $db->rollback();
            return false;
        }

        # 最終アクセス日時を更新
        if(!$db->updateUserLastAccess($session['user_id'])){
            if($transaction) $db->rollback();
            return false;
        }

        # ユーザ情報の取得
        if(!$user_data = $db->getUserDataById($session['user_id'])){
            if($transaction) $db->rollback();
            return false;
        }
        if(!$user_detail_data = $db->getUserDetailDataById($session['user_id'])){
            if($transaction) $db->rollback();
            return false;
        }

        # ステータス確認
        if((int)$user_data['status']===1){
            if($transaction) $db->rollback();
            return false;
        }
        # 削除確認
        if((int)$user_data['delf']===1){
            if($transaction) $db->rollback();
            return false;
        }

        # セッションIDの再交付と有効期限の延長
        $time = time();
        $new_session_id = $this->makeSessId($db);
        if(!$db->extensionUserSession($time, $session_id, $new_session_id)){
            insertErrorLog($db,500,"500 Internal Server Error","サーバーエラー発生。認証に失敗しています。サーバー不具合またはセッションIDの生成で重複が続いた可能性があります。");
            if($transaction) $db->rollback();
            return false;
        }
        $session_id = $new_session_id;
        $session = $db->getUserSession($session_id);

        # cookieセット
		setcookie(COOKIE_NAME_USER_LOGIN, $session_id, $time+LOGIN_USER_SESSION_TIME, "/");

        $this->user_data = $user_data;
        $this->user_detail_data = $user_detail_data;
        $this->session_id = $session_id;
        if($transaction) $db->commit();
        return true;
    }


    ####################################################################################################
    # ログイン(validationを除く)
    ####################################################################################################
    public function loginUser($db, $user_email, $user_password, $transaction=true){
        # 期限切れセッションの削除
        $db->cleanUserSession();

        if($transaction) $db->begin();
        # メールアドレス照合
        if(!$user_data = $db->getUserDataByEmail($user_email)){
            $this->login_err = "指定されたメールアドレスかパスワードのどちらか、もしくは両方が間違っています。";
            if($transaction) $db->rollback();
            return false;
        }

        # パスワード照合
        if(!verifyPassword($user_password, $user_data['user_password'])){
            $this->login_err = "指定されたメールアドレスかパスワードのどちらか、もしくは両方が間違っています。";
            if($transaction) $db->rollback();
            return false;
        }

        # 最終ログイン日時を更新
        if(!$db->updateUserLastLogin($user_data['user_id'])){
            if($transaction) $db->rollback();
            return false;
        }

        # ユーザ情報の取得
        if(!$user_data = $db->getUserDataById($user_data['user_id'])){
            if($transaction) $db->rollback();
            return false;
        }
        if(!$user_detail_data = $db->getUserDetailDataById($user_data['user_id'])){
            if($transaction) $db->rollback();
            return false;
        }

        # ステータス確認
        if((int)$user_data['status']===1){
            $this->login_err = "ログインが規制されています。";
            if($transaction) $db->rollback();
            return false;
        }
        # 削除確認
        if((int)$user_data['delf']===1){
            $this->login_err = "アカウントが凍結されています。";
            if($transaction) $db->rollback();
            return false;
        }

        # ログインセッションの登録
        $time = time();
        $session_id = $this->makeSessId($db);
        if(!$db->insertUserSession($time, $user_data['user_id'], $session_id)){
            $this->login_err = "ログインエラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","サーバーエラー発生。ログインに失敗しています。サーバー不具合またはセッションIDの生成で重複が続いた可能性があります。error_message：".$this->login_err);
            if($transaction) $db->rollback();
            return false;
        }

        # cookieのセット
		setcookie(COOKIE_NAME_USER_LOGIN, $session_id, $time+LOGIN_USER_SESSION_TIME, "/");
        
        $this->user_data = $user_data;
        $this->user_detail_data = $user_detail_data;
        $this->session_id = $session_id;
        if($transaction) $db->commit();
        return true;
    }


    ####################################################################################################
    # ログアウト
    ####################################################################################################
    public function logoutUser($db){
        # ログインセッションの削除
        if(!$db->killUserSessionDataById($this->user_data()['user_id'])){
            insertErrorLog($db,500,"500 Internal Server Error","サーバーエラー発生。ログアウトに失敗しています。");
            return false;
        }

        # セッション変数削除
        $_SESSION = array();

        # cookie削除
        setcookie(COOKIE_NAME_USER_LOGIN, "", time()-3600, "/");

        return true;
    }


    ####################################################################################################
    # メールアドレス認証(validationを除く)
    ####################################################################################################
    public function authEmailSession($db, $session_id, $transaction=true){
        # 期限切れパスワードセッションの削除
        $db->cleanUserEmailSession();

        if($transaction) $db->begin();

        # パスワードセッション取得
        if(!$email_session = $db->getUserEmailSession($session_id)){
            if($transaction) $db->rollback();
            return false;
        }

        $this->email_session_id = $session_id;
        if($transaction) $db->commit();
        return $email_session['user_email'];
    }
    
    public function registEmailSession($db, $user_email, $transaction=true){
        if($transaction) $db->begin();

        # 登録されているメールアドレスではないか確認
        if($user_data = $db->getUserDataByEmail($user_email)){
            $this->email_auth_err = "このメールアドレスは登録されています。";
            if($transaction) $db->rollback();
            return false;
        }

        # セッションを挿入
        $time = time();
        $session_id = $this->makeEmailSessId($db);
        if(!$db->replaceUserEmailSession($time, $user_email, $session_id)){
            $this->email_auth_err = "メール認証エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","サーバーエラー発生。メール認証に失敗しています。サーバー不具合またはセッションIDの生成で重複が続いた可能性があります。error_message：".$this->email_auth_err);
            if($transaction) $db->rollback();
            return false;
        }

        # メール送信
        $site_name = SITE_NAME;
        $url = HTTP_USER.'main_regist/'.$session_id.'/';
        //error_log($url);
        $time_limit = date("Y年m月d日 H時i分", $time+EMAIL_USER_SESSION_TIME);
        $from = FROM_EMAIL;
        $to = $user_email;
        $subject = "{$site_name} 本登録URL送信";
        $fromName = "{$site_name}";
        $copyright = COPYRIGHT_EMAIL;
        $body = <<< EOS
{$site_name}をご利用いただき、誠にありがとうございます。
以下のURLにアクセスいただき、
有効期限までに本登録を行なってください。

{$url}
有効期限：{$time_limit}


※ 本メールは{$site_name}のアカウントやサービスの重要な変更についてお知らせするためにお送りしています。
※ お心当たりがない場合は、誠に恐れ入りますが、破棄していただけますようお願いいたします。

※ 本メールは{$site_name}より自動でお送りしています。
※ 送信専用のアドレスから送信しているため、送信元のアドレス宛にご返信いただいても受信できません。予めご了承ください。
※ お問い合わせは、{$site_name}の問い合わせフォームよりお願いいたします。

{$copyright}

EOS;
    
        if(!send_mail($to, $subject, $body, $from, $fromName)){
            $this->email_auth_err = "メール認証エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","メール送信エラー発生。メール認証のメール送信に失敗しています。error_message：".$this->email_auth_err);
            if($transaction) $db->rollback();
            return false;
        }

        $this->pass_session_id = $session_id;
        if($transaction) $db->commit();
        return true;
    }


    ####################################################################################################
    # 新規登録(validationを除く)
    ####################################################################################################

    public function registUser($db, $user_name, $user_email, $user_password, $user_position=0, $user_details=[], $transaction=true){
        if($transaction) $db->begin();

        # ユーザ取得（→validationでも行う）
        if($db->getUserDataByEmail($user_email)){
            $this->regist_guest_err = "指定されたメールアドレスはすでに使用されています。";
            if($transaction) $db->rollback();
            return false;
        }
        # ユーザ詳細取得（→validationでも行う）
        if(isset($user_details['user_key']) && $db->getUserDetailDataByKey($user_details['user_key'])){
            $this->regist_guest_err = "指定されたユーザーIDはすでに使用されています。";
            if($transaction) $db->rollback();
            return false;
        }

        # 登録(user)
        if(!$user_id = $db->insertUserData($user_name, $user_email, $user_password, $user_position)){
            $this->regist_guest_err = "登録エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            if($transaction) $db->rollback();
            return false;
        }
        # 登録(user_detail)
        if(!$db->insertUserDetailData($user_id, $user_details)){
            $this->regist_guest_err = "登録エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","サーバーエラー発生。新規登録に失敗しています。error_message：".$this->regist_guest_err);
            if($transaction) $db->rollback();
            return false;
        }

        # パスワードセッション削除
        if(!$db->killUserEmailSessionDataById($user_email)){
            $this->regist_guest_err = "登録エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","サーバーエラー発生。新規登録に失敗しています。error_message：".$this->regist_guest_err);
            if($transaction) $db->rollback();
            return false;
        }

        # メール送信
        $site_name = SITE_NAME;
        $from = FROM_EMAIL;
        $to = $user_email;
        $subject = "{$site_name} ユーザー登録完了";
        $fromName = "{$site_name}";
        $url = HTTP_USER.'login/';
        $copyright = COPYRIGHT_EMAIL;
        $body = <<< EOS
{$user_name} 様

この度は{$site_name}に登録いただき、誠にありがとうございます。
以下のURLにアクセスいただき、
ご登録いただきましたメールアドレスとパスワードにてログインしてご利用ください。

{$url}


※ 本メールは{$site_name}のアカウントやサービスの重要な変更についてお知らせするためにお送りしています。
※ お心当たりがない場合は、誠に恐れ入りますが、破棄していただけますようお願いいたします。

※ 本メールは{$site_name}より自動でお送りしています。
※ 送信専用のアドレスから送信しているため、送信元のアドレス宛にご返信いただいても受信できません。予めご了承ください。
※ お問い合わせは、{$site_name}の問い合わせフォームよりお願いいたします。

{$copyright}

EOS;
    
        if(!send_mail($to, $subject, $body, $from, $fromName)){
            $this->regist_guest_err = "登録エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","メール送信エラー発生。新規登録のメール送信に失敗しています。error_message：".$this->regist_guest_err);
            if($transaction) $db->rollback();
            return false;
        }

        if($transaction) $db->commit();
        return $user_id;
    }


    ####################################################################################################
    # ゲスト登録(validationを除く)
    ####################################################################################################

    public function registUserAsGuest($db, $guest_user_nickname, $transaction=true){
        if($transaction) $db->begin();

        # ゲストユーザーキー発行
        $guest_user_key = $this->makeGuestUserKey($db);
        $guest_user_email = "guest".$guest_user_key."@example.com";

        # ユーザ取得（→validationでも行う）
        if($db->getUserDataByEmail($guest_user_email)){
            $this->regist_err = "ゲスト登録エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            if($transaction) $db->rollback();
            return false;
        }

        # 登録(user)
        if(!$guest_user_id = $db->insertUserData("ゲスト", $guest_user_email, makeRandomId(64), 0)){
            $this->regist_err = "ゲスト登録エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            if($transaction) $db->rollback();
            return false;
        }
        # 登録(user_detail)
        $guest_user_details = [
            "user_key"=>$guest_user_key,
            "user_nickname"=>$guest_user_nickname,
            "guest_flg"=>1,
        ];
        if(!$db->insertUserDetailData($guest_user_id, $guest_user_details)){
            $this->regist_err = "ゲスト登録エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","サーバーエラー発生。新規ゲスト登録に失敗しています。error_message：".$this->regist_err);
            if($transaction) $db->rollback();
            return false;
        }

        if($transaction) $db->commit();
        return $guest_user_id;
    }



    ####################################################################################################
    # プロフィール編集(validationを除く)
    ####################################################################################################
    public function updateUser($db, $user_name, $user_position, $user_details, $transaction=true){
        if($transaction) $db->begin();

        # 更新(user)
        if($user_name!==null && !$db->updateUserName($this->user_data['user_id'], $user_name)){
            $this->edit_err = "プロフィール編集エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","サーバーエラー発生。プロフィール編集に失敗しています。性別の更新に失敗しています。error_message：".$this->edit_err);
            if($transaction) $db->rollback();
            return false;            
        }
        if($user_position!==null && !$db->updateUserPosition($this->user_data['user_id'], $user_position)){
            $this->edit_err = "プロフィール編集エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","サーバーエラー発生。プロフィール編集に失敗しています。性別の更新に失敗しています。error_message：".$this->edit_err);
            if($transaction) $db->rollback();
            return false;            
        }
        if($user_details!==null && $user_details!==[] && !$db->updateUserDetail($this->user_data['user_id'],$user_details)){
            $this->edit_err = "プロフィール編集エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","サーバーエラー発生。プロフィール編集に失敗しています。性別の更新に失敗しています。error_message：".$this->edit_err);
            if($transaction) $db->rollback();
            return false;            
        }

        # メール送信
        $site_name = SITE_NAME;
        $user_name = $this->user_data['user_name'];
        $from = FROM_EMAIL;
        $to = $this->user_data['user_email'];
        $subject = "{$site_name} プロフィール編集完了";
        $fromName = "{$site_name}";
        $copyright = COPYRIGHT_EMAIL;
        $body = <<< EOS
{$user_name} 様

{$site_name}をご利用いただき、誠にありがとうございます。
プロフィールの編集が完了しました。


※ 本メールは{$site_name}のアカウントやサービスの重要な変更についてお知らせするためにお送りしています。
※ お心当たりがない場合は、誠に恐れ入りますが、破棄していただけますようお願いいたします。

※ 本メールは{$site_name}より自動でお送りしています。
※ 送信専用のアドレスから送信しているため、送信元のアドレス宛にご返信いただいても受信できません。予めご了承ください。
※ お問い合わせは、{$site_name}の問い合わせフォームよりお願いいたします。

{$copyright}

EOS;
    
        if(!send_mail($to, $subject, $body, $from, $fromName)){
            $this->edit_err = "登録エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","メール送信エラー発生。プロフィール編集のメール送信に失敗しています。error_message：".$this->edit_err);
            if($transaction) $db->rollback();
            return false;
        }

        if($transaction) $db->commit();
        return true;
    }


    ####################################################################################################
    # 退会処理
    ####################################################################################################
    public function leaveUser($db,$leave_reason,$leave_detail, $transaction=true){
        # 期限切れセッションの削除
        $db->cleanUserSession();

        if($transaction) $db->begin();

        # ログインされているか確認
        if(!isset($this->user_data) || !isset($this->session_id)){
            $this->leave_err = "ログインされていません。ログインしてください。";
            if($transaction) $db->rollback();
            return false;
        }

        # 退会アンケート登録
        if(!$db->insertUserLeaveData($this->user_data['user_id'],$leave_reason,$leave_detail)){
            $this->leave_err = "退会エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            if($transaction) $db->rollback();
            return false;
        }

        # ユーザー削除
        if(!$db->deleteUserData($this->user_data['user_id'])){
            $this->leave_err = "退会エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            if($transaction) $db->rollback();
            return false;
        }

        # その他ユーザー情報削除(この部分は毎回要検討)
        // if(!deleteImageData($db,IMG_CATE_USER,IMG_SUBCATE_USER_ICON,(int)$this->user_data['user_id'])){
        //     $this->leave_err = "退会エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
        //     if($transaction) $db->rollback();
        //     return false;
        // }
        // if(!$db->deleteQuestionCommentDataForUserId($this->user_data['user_id'])){
        //     $this->leave_err = "退会エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
        //     if($transaction) $db->rollback();
        //     return false;
        // }
        // if(!$db->deleteQuestionGoodDataForUserId($this->user_data['user_id'])){
        //     $this->leave_err = "退会エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
        //     if($transaction) $db->rollback();
        //     return false;
        // }
        // if(!$db->deleteFollowDataForFromUserId($this->user_data['user_id'])){
        //     $this->leave_err = "退会エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
        //     if($transaction) $db->rollback();
        //     return false;
        // }
        // if(!$db->deleteFollowDataForToUserId($this->user_data['user_id'])){
        //     $this->leave_err = "退会エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
        //     if($transaction) $db->rollback();
        //     return false;
        // }
        
        # メール送信
        $site_name = SITE_NAME;
        $user_name = $this->user_data['user_name'];
        $from = FROM_EMAIL;
        $to = $this->user_data['user_email'];
        $subject = "{$site_name} プロフィール編集完了";
        $fromName = "{$site_name}";
        $copyright = COPYRIGHT_EMAIL;
        $body = <<< EOS
{$user_name} 様

{$site_name}の退会手続きが終了いたしました。
この度は{$site_name}をご利用頂いただきまして、誠にありがとうございました。

ご不明な点やご質問などがありましたら、問い合わせフォームよりお問い合わせ下さい。


※ 本メールは{$site_name}のアカウントやサービスの重要な変更についてお知らせするためにお送りしています。
※ お心当たりがない場合は、誠に恐れ入りますが、破棄していただけますようお願いいたします。

※ 本メールは{$site_name}より自動でお送りしています。
※ 送信専用のアドレスから送信しているため、送信元のアドレス宛にご返信いただいても受信できません。予めご了承ください。
※ お問い合わせは、{$site_name}の問い合わせフォームよりお願いいたします。

{$copyright}

EOS;
    
        if(!send_mail($to, $subject, $body, $from, $fromName)){
            $this->leave_err = "退会エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","メール送信エラー発生。プロフィール編集のメール送信に失敗しています。error_message：".$this->edit_err);
            if($transaction) $db->rollback();
            return false;
        }

        if($transaction) $db->commit();
        return true;
    }


    ####################################################################################################
    # パスワードセッションの認証
    ####################################################################################################
    public function authPasswordSession($db, $session_id, $transaction=true){
        # 期限切れパスワードセッションの削除
        $db->cleanUserPasswordSession();

        if($transaction) $db->begin();

        # パスワードセッション取得
        if(!$password_session = $db->getUserPasswordSession($session_id)){
            if($transaction) $db->rollback();
            return false;
        }

        # ユーザ取得
        if(!$user_data = $db->getUserDataById($password_session['user_id'])){
            if($transaction) $db->rollback();
            return false;
        }

        $this->pass_user_data = $user_data;
        $this->pass_session_id = $session_id;
        if($transaction) $db->commit();
        return true;
    }


    ####################################################################################################
    # パスワード忘れ
    ####################################################################################################
    public function forgotUserPassword($db, $forgot_email, $transaction=true){
        if($transaction) $db->begin();

        # 登録されているメールアドレスか確認
        if(!$user_data = $db->getUserDataByEmail($forgot_email)){
            $this->forgot_err = "このメールアドレスは登録されていません。";
            if($transaction) $db->rollback();
            return false;
        }

        # セッションを挿入
        $time = time();
        $session_id = $this->makePassSessId($db);
        if(!$db->replaceUserPasswordSession($time, $user_data['user_id'], $session_id)){
            $this->forgot_err = "パスワード忘れ申請エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","サーバーエラー発生。パスワード忘れ申請に失敗しています。サーバー不具合またはセッションIDの生成で重複が続いた可能性があります。error_message：".$this->forgot_err);
            if($transaction) $db->rollback();
            return false;
        }

        # メール送信
        $site_name = SITE_NAME;
        $user_name = $user_data['user_name'];
        $url = HTTP_USER.'reset/'.$session_id.'/';
        $time_limit = date("Y年m月d日 H時i分", $time+FORGOT_USER_SESSION_TIME);
        $from = FROM_EMAIL;
        $to = $forgot_email;
        $subject = "{$site_name} パスワード変更URL送信";
        $fromName = "{$site_name}";
        $copyright = COPYRIGHT_EMAIL;
        $body = <<< EOS
{$user_name} 様

{$site_name}をご利用いただき、誠にありがとうございます。
以下のURLにアクセスいただき、
有効期限までに新しいパスワードを設定してください。

{$url}
有効期限：{$time_limit}

※ 本メールは{$site_name}のアカウントやサービスの重要な変更についてお知らせするためにお送りしています。
※ お心当たりがない場合は、誠に恐れ入りますが、破棄していただけますようお願いいたします。

※ 本メールは{$site_name}より自動でお送りしています。
※ 送信専用のアドレスから送信しているため、送信元のアドレス宛にご返信いただいても受信できません。予めご了承ください。
※ お問い合わせは、{$site_name}の問い合わせフォームよりお願いいたします。

{$copyright}

EOS;
    
        if(!send_mail($to, $subject, $body, $from, $fromName)){
            $this->forgot_err = "パスワード忘れ申請エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","メール送信エラー発生。パスワード忘れ申請のメール送信に失敗しています。error_message：".$this->forgot_err);
            if($transaction) $db->rollback();
            return false;
        }

        $this->pass_session_id = $session_id;
        if($transaction) $db->commit();
        return true;
    }


    ####################################################################################################
    # パスワードリセット
    ####################################################################################################
    public function resetUserPassword($db, $new_password, $transaction=true){
        if($transaction) $db->begin();

        $user_id = $this->pass_user_data()['user_id'];
        # パスワード変更処理
        if(!$db->changeUserPassword($user_id, $new_password)){
            $this->reset_err = "パスワード変更エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","サーバーエラー発生。パスワード変更に失敗しています。error_message：".$this->reset_err);
            if($transaction) $db->rollback();
            return false;
        }

        # パスワードセッション削除
        if(!$db->killUserPasswordSessionDataById($user_id)){
            $this->reset_err = "パスワード変更エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","サーバーエラー発生。パスワード変更に失敗しています。error_message：".$this->reset_err);
            if($transaction) $db->rollback();
            return false;
        }

        # メール送信
        $site_name = SITE_NAME;
        $user = $this->pass_user_data();
        $user_name = $user['user_name'];
        $user_email = $user['user_email'];
        $from = FROM_EMAIL;
        $to = $user['user_email'];
        $subject = "{$site_name} パスワード変更完了";
        $fromName = "{$site_name}";
        $url = HTTP_USER.'login/';
        $copyright = COPYRIGHT_EMAIL;
        $body = <<< EOS
{$user_name} 様

{$site_name}をご利用いただき、誠にありがとうございます。
パスワードの再設定が完了いたしました。
以下のURLにアクセスいただき、
ご登録いただきましたメールアドレスとパスワードにてログインしてご利用ください。

{$url}


※ 本メールは{$site_name}のアカウントやサービスの重要な変更についてお知らせするためにお送りしています。
※ お心当たりがない場合は、誠に恐れ入りますが、破棄していただけますようお願いいたします。

※ 本メールは{$site_name}より自動でお送りしています。
※ 送信専用のアドレスから送信しているため、送信元のアドレス宛にご返信いただいても受信できません。予めご了承ください。
※ お問い合わせは、{$site_name}の問い合わせフォームよりお願いいたします。

{$copyright}

EOS;
    
        if(!send_mail($to, $subject, $body, $from, $fromName)){
            $this->reset_err = "パスワード変更エラーが発生しました。しばらく時間をおいてもう一度お試しください。";
            insertErrorLog($db,500,"500 Internal Server Error","メール送信エラー発生。パスワード変更のメール送信に失敗しています。error_message：".$this->reset_err);
            if($transaction) $db->rollback();
            return false;
        }

        $this->pass_user_data = null;
        $this->pass_session_id = null;
        if($transaction) $db->commit();
        return true;
    }


}
