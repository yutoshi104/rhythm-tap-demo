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
    if($user_auth->authSession($db)){
        if((int)$user_auth->user_data()['status'] === 0){
            // リダイレクト(ログイン済み)
            header("Location: /");
            exit();
        }
    }

    # プロフィール画像取得
    $icon_urls = getUserIcon($db, isset($login_flg) && $login_flg===true ? $user_auth->user_data()['user_id'] : null);
    $user_icon_url = $icon_urls[0];
    $user_icon_url_thumb = $icon_urls[1];

    # 変数定義
    $email = null;

    # メール認証フォームが送られてきたとき
    if(isset($_POST["mode"]) && $_POST["mode"] === "email_auth"){

        # csrf対策
        $token = filter_input(INPUT_POST, 'csrf_token');
        if (!isset($_SESSION['csrf_token']) || $token!==$_SESSION['csrf_token']){
            displayUserErrorExit(400,"400 Bad Request","不正なアクセスです。");
        }
        unset($_SESSION['csrf_token']);

        # 値取得
        $mode = filter_input(INPUT_POST, 'mode');
        $email = filter_input(INPUT_POST, 'email');

        # バリデーション
        $err = [];
        if(!isValid($email)){
            $err[] = "メールアドレスが入力されていません。";
        }
        if(!isEmail($email)){
            $err[] = "正しいメールアドレスではありません。";
        }
        if($db->getUserDataByEmail($email)){
            $err[] = "指定されたメールアドレスはすでに使用されています。";
        }

        if($err!==[]){
            $email_auth_err = implode("",$err);
        }else{
            # 登録処理
            if($user_auth->registEmailSession($db, $email)){
                $db->insertUserLog("EMAIL AUTH USER", ($user_auth->user_data()!==null ? $user_auth->user_data()['user_id'] : null), "send url (user_email=".$email.")");
                # View
                $page = [
                    "title"=>"新規登録 URL送信完了 - ".SITE_NAME,
                    "current"=>0,
                    "auth"=>"email_auth_comp",
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
                $view->addParam("email_auth_err", isset($email_auth_err) ? $email_auth_err : null);
                $view->addParam("csrf_token", setToken());
                $view->addTemplate(VIEW_DIR_USER."/header.inc");
                $view->addTemplate(VIEW_DIR_USER."/user/auth/email_auth_comp.inc");
                $view->addTemplate(VIEW_DIR_USER."/footer_empty.inc");
                $view->display();
                exit();
            }
            $email_auth_err = $user_auth->email_auth_err();
        }
    }



    # View
    $metas = [
        "title"=>"新規登録 - ".SITE_NAME,
        "description"=>SITE_NAME."の新規登録ページです。".META_DESCRIPTION,
        "keywords"=>META_KEYWORDS,
        "twitter:card"=>META_TWITTER_CARD,
        "twitter:site"=>META_TWITTER_ID,
        "twitter:creator"=>META_TWITTER_ID,
        "twitter:title"=>"新規登録 - ".SITE_NAME,
        "twitter:description"=>SITE_NAME."の新規登録ページです。".META_DESCRIPTION,
    ];
    $ogps = [
        "og:title"=>"新規登録 - ".SITE_NAME,
        "og:description"=>SITE_NAME."の新規登録ページです。".META_DESCRIPTION,
        "og:type"=>META_TYPE,
        "og:url"=>META_URL,
        "og:image"=>META_TWITTER_IMAGE,
        "og:site_name"=>META_SITE_NAME,
        "og:locale"=>META_LOCALE,
        "fb:app_id"=>META_FACEBOOK_APP_ID,
    ];
    $page = [
        "title"=>"新規登録 - ".SITE_NAME,
        "current"=>0,
        "auth"=>"regist",
        "user_data"=>$user_auth->user_data()!==null ? $user_auth->user_data() : null,
        "user_detail_data"=>$user_auth->user_detail_data()!==null ? $user_auth->user_detail_data() : null,
        "user_icon_url"=>$user_icon_url,
        "user_icon_url_thumb"=>$user_icon_url_thumb,
        "head_css"=>["/assets/css/signin.css"],
        "head_js"=>[],
        "foot_js"=>[]
    ];
    $view = new View();
    $view->addParam("metas", $metas);
    $view->addParam("ogps", $ogps);
    $view->addParam("page", $page);
    $view->addParam("email", isset($email) ? $email : "");
    $view->addParam("email_auth_err", isset($email_auth_err) ? $email_auth_err : null);
    $view->addParam("csrf_token", setToken());
    $view->addTemplate(VIEW_DIR_USER."/header.inc");
    $view->addTemplate(VIEW_DIR_USER."/user/auth/email_auth.inc");
    $view->addTemplate(VIEW_DIR_USER."/footer_empty.inc");
    $view->display();