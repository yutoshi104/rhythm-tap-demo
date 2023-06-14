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
        $login_flg = false;
    }else{
        $login_flg = true;
    }

    # プロフィール画像取得
    $icon_urls = getUserIcon($db, isset($login_flg) && $login_flg===true ? $user_auth->user_data()['user_id'] : null);
    $user_icon_url = $icon_urls[0];
    $user_icon_url_thumb = $icon_urls[1];


    # パスワード忘れフォームが送られてきたとき
    if(isset($_POST["mode"]) && $_POST["mode"] === "forgot"){
        # csrf対策
        $token = filter_input(INPUT_POST, 'csrf_token');
        if (!isset($_SESSION['csrf_token']) || $token!==$_SESSION['csrf_token']){
            displayUserErrorExit(400,"400 Bad Request","不正なアクセスです。");
        }
        unset($_SESSION['csrf_token']);

        # 値取得
        $email = filter_input(INPUT_POST, 'email');

        # バリデーション
        $err = [];
        if(!isValid($email)){
            $err[] = "メールアドレスが入力されていません。";
        }
        if(!isEmail($email)){
            $err[] = "正しいメールアドレスではありません。";
        }

        if(!$err){
            if($user_auth->forgotUserPassword($db, $email)){
                $db->insertUserLog("FORGOT USER PASSWORD", ($user_auth->user_data()!==null ? $user_auth->user_data()['user_id'] : null), "send url");
                $page = [
                    "title"=>"パスワード忘れ URL送信完了 - ".SITE_NAME,
                    "current"=>0,
                    // "auth"=>"forgot_comp",
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
                $view->addParam("page", $page);
                $view->addParam("csrf_token", setToken());
                $view->addTemplate(VIEW_DIR_USER."/header.inc");
                $view->addTemplate(VIEW_DIR_USER."/user/auth/forgot_comp.inc");
                $view->addTemplate(VIEW_DIR_USER."/footer_empty.inc");
                $view->display();
                exit();
            }
            $forgot_err = $user_auth->forgot_err();
        }else{
            $forgot_err = implode("",$err);
        }
    }








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
        "title"=>"パスワード忘れ - ".SITE_NAME,
        "current"=>0,
        // "auth"=>"forgot",
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
    $view->addParam("forgot_err", isset($forgot_err) ? $forgot_err : null);
    $view->addParam("csrf_token", setToken());
    $view->addTemplate(VIEW_DIR_USER."/header.inc");
    $view->addTemplate(VIEW_DIR_USER."/user/auth/forgot.inc");
    $view->addTemplate(VIEW_DIR_USER."/footer_empty.inc");
    $view->display();