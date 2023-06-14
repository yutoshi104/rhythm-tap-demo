<?php

    session_start();
    session_regenerate_id(true);

    # include
    $lib_dir = __DIR__."/../../../lib";
    require_once($lib_dir."/common_inc.php");

    # DB connect
    $db = new Db();
    if( $db->init_master() === false ) {
        sendErrorLog(500,"500 Internal Server Error","サーバーエラー発生。DBの接続に失敗しています。error_message：".$db->getErr());
        displayUserErrorExit(500,"500 Internal Server Error","サーバーエラーが発生しています。しばらく時間をおいてもう一度アクセスしてください。しばらく経ってもエラーが解消しない場合、管理者へご連絡ください。");
    }

    $user_auth = new UserAuth();

    # login session
    if(!$user_auth->authSession($db)){
        $_SESSION['login_err'] = "ログインされていません。ログインしてください。";
        header("Location: /login/?r=".urlencode($_SERVER['REQUEST_URI']));
        exit();
    }else{
        $login_flg = true;
    }

    # プロフィール画像取得
    $icon_urls = getUserIcon($db, isset($login_flg) && $login_flg===true ? $user_auth->user_data()['user_id'] : null);
    $user_icon_url = $icon_urls[0];
    $user_icon_url_thumb = $icon_urls[1];




    # 変数定義
    $leave_reason = null;
    $leave_detail = null;

    # 退会フォームが送られてきたとき
    if(isset($_POST["mode"]) && $_POST["mode"] === "leave"){
        # csrf対策
        $token = filter_input(INPUT_POST, 'csrf_token');
        if (!isset($_SESSION['csrf_token']) || $token!==$_SESSION['csrf_token']){
            displayUserErrorExit(400,"400 Bad Request","不正なアクセスです。");
        }
        unset($_SESSION['csrf_token']);

        # 値取得
        $mode = filter_input(INPUT_POST, 'mode');
        $leave_reason = isset($_POST['leave_reason']) || $_POST['leave_reason']==="" ? (int)$_POST['leave_reason'] : null;
        $leave_detail = isset($_POST['leave_detail']) ? $_POST['leave_detail'] : "";

        # バリデーション
        $err = [];
        if(isset($leave_reason) && $leave_reason!==0 && (!is_numeric($leave_reason) || !array_key_exists($leave_reason,LEAVE_REASON))){
            $err[] = "不正な理由です。";
        }

        if($err!==[]){
            $leave_err = implode("",$err);
        }else{
            # View
            $page = [
                "title"=>"退会 確認 - ".SITE_NAME,
                "current"=>0,
                "auth"=>"leave",
                "user_data"=>$user_auth->user_data()!==null ? $user_auth->user_data() : null,
                "user_detail_data"=>$user_auth->user_detail_data()!==null ? $user_auth->user_detail_data() : null,
                "user_icon_url"=>$user_icon_url,
                "user_icon_url_thumb"=>$user_icon_url_thumb,
                "head_css"=>["/assets/css/signin.css"],
                "head_js"=>[],
                "foot_js"=>[]
            ];
            $view = new View();
            $view->addParam("page", $page);
            $view->addParam("mode", "confirm");
            $view->addParam("leave_reason", isset($leave_reason) ? $leave_reason : null);
            $view->addParam("leave_detail", $leave_detail!=="" ? $leave_detail : null);
            $view->addParam("leave_err", isset($leave_err) ? $leave_err : null);
            $view->addParam("csrf_token", setToken());
            $view->addTemplate(VIEW_DIR_USER."/header.inc");
            $view->addTemplate(VIEW_DIR_USER."/user/auth/leave.inc");
            $view->addTemplate(VIEW_DIR_USER."/footer_empty.inc");
            $view->display();
            exit();
        }
    }

    # 確認フォームが送られてきたとき
    if(isset($_POST["mode"]) && $_POST["mode"] === "confirm"){
        # csrf対策
        $token = filter_input(INPUT_POST, 'csrf_token');
        if (!isset($_SESSION['csrf_token']) || $token!==$_SESSION['csrf_token']){
            displayUserErrorExit(400,"400 Bad Request","不正なアクセスです。");
        }
        unset($_SESSION['csrf_token']);

        $err = [];
        # google recaptcha
        // $recaptcha = htmlspecialchars($_POST["g-recaptcha-response"],ENT_QUOTES,'UTF-8');
        // if(isset($recaptcha)){
        //     $captcha = $recaptcha;
        //     $secretKey = "6LeZg2McAAAAAJm7jstWMHZoKtNcbmUP90Uz6QzF";
        //     $resp = @file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$captcha}");
        //     $resp_result = json_decode($resp,true);
        //     if(intval($resp_result["success"]) !== 1){
        //         displayUserErrorExit(400,"400 Bad Request","不正なアクセスです。");
        //     }
        // }else{
        //     $err[] = "チェックを入れてください。";
        // }

        # 値取得
        $mode = filter_input(INPUT_POST, 'mode');
        $leave_reason = isset($_POST['leave_reason']) || $_POST['leave_reason']==="" ? (int)$_POST['leave_reason'] : null;
        $leave_detail = isset($_POST['leave_detail']) ? $_POST['leave_detail'] : "";
        $password = isset($_POST['password']) ? $_POST['password'] : "";

        # バリデーション
        if(isset($leave_reason) && $leave_reason!==0 && (!is_numeric($leave_reason) || !array_key_exists($leave_reason,LEAVE_REASON))){
            $err[] = "不正な理由です。";
        }
        if(!isValid($password)){
            $err[] = "パスワードが入力されていません。";
        }
        if(!verifyPassword($password, $user_auth->user_data()['user_password'])){
            $err[] = "パスワードが違います。";
        }

        if($err===[]){
            # 退会処理
            if(!$user_auth->leaveUser($db,$leave_reason,$leave_detail)){
                $leave_err = $user_auth->leave_err();
                goto err;
            }
            $db->insertUserLog("LEAVE USER", ($user_auth->user_data()!==null ? $user_auth->user_data()['user_id'] : null));
            $user_auth->logoutUser($db);
            session_destroy();
            // header("Location: /");
            // exit();
            # View
            $page = [
                "title"=>"退会 完了 - ".SITE_NAME,
                "current"=>0,
                "auth"=>"leave",
                "user_data"=>$user_auth->user_data()!==null ? $user_auth->user_data() : null,
                "user_detail_data"=>$user_auth->user_detail_data()!==null ? $user_auth->user_detail_data() : null,
                "user_icon_url"=>$user_icon_url,
                "user_icon_url_thumb"=>$user_icon_url_thumb,
                "head_css"=>["/assets/css/signin.css"],
                "head_js"=>[],
                "foot_js"=>[]
            ];
            $view = new View();
            $view->addParam("page", $page);
            $view->addTemplate(VIEW_DIR_USER."/header.inc");
            $view->addTemplate(VIEW_DIR_USER."/user/auth/leave_comp.inc");
            $view->addTemplate(VIEW_DIR_USER."/footer.inc");
            $view->display();
            exit();
        }else{
            $leave_err = implode("",$err);;
        }
    }

    # 戻るフォームが送られてきたとき
    if(isset($_POST["mode"]) && $_POST["mode"] === "back"){

        # csrf対策
        $token = filter_input(INPUT_POST, 'csrf_token');
        if (!isset($_SESSION['csrf_token']) || $token!==$_SESSION['csrf_token']){
            displayUserErrorExit(400,"400 Bad Request","不正なアクセスです。");
        }
        unset($_SESSION['csrf_token']);

        # 値取得
        $mode = "leave";
        $leave_reason = isset($_POST['leave_reason']) || $_POST['leave_reason']==="" ? (int)$_POST['leave_reason'] : null;
        $leave_detail = isset($_POST['leave_detail']) ? $_POST['leave_detail'] : "";
    }


    err:



    # View
    // $metas = [
    //     "title"=>META_TITLE,
    //     "description"=>META_DESCRIPTION,
    //     "keywords"=>META_KEYWORDS,
    //     "twitter:card"=>META_TWITTER_CARD,
    //     "twitter:site"=>META_TWITTER_ID,
    //     "twitter:creator"=>META_TWITTER_ID,
    //     "twitter:title"=>META_TITLE,
    //     "twitter:description"=>META_DESCRIPTION,
    // ];
    // $ogps = [
    //     "og:title"=>META_TITLE,
    //     "og:description"=>META_DESCRIPTION,
    //     "og:type"=>META_TYPE,
    //     "og:url"=>META_URL,
    //     "og:image"=>META_TWITTER_IMAGE,
    //     "og:site_name"=>META_SITE_NAME,
    //     "og:locale"=>META_LOCALE,
    //     "fb:app_id"=>META_FACEBOOK_APP_ID,
    // ];
    $page = [
        "title"=>"退会 - ".SITE_NAME,
        "current"=>0,
        // "auth"=>"leave",
        "login_flg"=>$login_flg,
        "user_data"=>$user_auth->user_data()!==null ? $user_auth->user_data() : null,
        "user_detail_data"=>$user_auth->user_detail_data()!==null ? $user_auth->user_detail_data() : null,
        "user_icon_url"=>$user_icon_url,
        "user_icon_url_thumb"=>$user_icon_url_thumb,
        "head_css"=>["/assets/css/signin.css"],
        "head_js"=>[],
        "foot_js"=>[]
    ];
    $view = new View();
    // $view->addParam("metas", $metas);
    // $view->addParam("ogps", $ogps);
    $view->addParam("page", $page);
    $view->addParam("mode", isset($mode) ? $mode : "leave");
    $view->addParam("leave_reason", isset($leave_reason) ? $leave_reason : null);
    $view->addParam("leave_detail", $leave_detail!=="" ? $leave_detail : null);
    $view->addParam("leave_err", isset($leave_err) ? $leave_err : null);
    $view->addParam("csrf_token", setToken());
    $view->addTemplate(VIEW_DIR_USER."/header.inc");
    $view->addTemplate(VIEW_DIR_USER."/user/auth/leave.inc");
    $view->addTemplate(VIEW_DIR_USER."/footer_empty.inc");
    $view->display();