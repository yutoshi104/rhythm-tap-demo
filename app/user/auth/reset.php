<?php

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

    # password_sessionの確認
    $password_session_id = isset($_SESSION['psid']) ? $_SESSION['psid'] : null;
    if(!$user_auth->authPasswordSession($db,$password_session_id)){
        displayUserErrorExit(400,"400 Bad Request","不正なアクセスです。");
    }

    # パスワード再設定フォームが送られてきたとき
    if(isset($_POST["mode"]) && $_POST["mode"] === "reset"){
        # csrf対策
        $token = filter_input(INPUT_POST, 'csrf_token');
        if (!isset($_SESSION['csrf_token']) || $token!==$_SESSION['csrf_token']){
            displayUserErrorExit(400,"400 Bad Request","不正なアクセスです。");
        }
        unset($_SESSION['csrf_token']);

        # 値取得
        $password = filter_input(INPUT_POST, 'password');
        $password_conf = filter_input(INPUT_POST, 'password_conf');

        # バリデーション
        $err = [];
        if(!isValid($password)){
            $err[] = "パスワードが入力されていません。";
        }
        if(!isValidStrLen($password,8,32)){
            $err[] = "パスワードは8文字以上32文字以下で設定してください。";
        }
        if(!isPassword($password)){
            $err[] = "パスワードは少なくとも英数をそれぞれ1文字以上含んでください。";
        }
        if(!isValid($password_conf)){
            $err[] = "確認用パスワードが入力されていません。";
        }
        if($password !== $password_conf){
            $err[] = "確認用パスワードが一致しません。";
        }

        if(!$err){
            if($user_auth->resetUserPassword($db, $password)){
                $db->insertUserLog("RESET USER PASSWORD", ($user_auth->user_data()!==null ? $user_auth->user_data()['user_id'] : null));
                # logout session
                if($login_flg && !$user_auth->logoutUser($db)){
                    displayUserErrorExit(500,"500 Internal Server Error","ログアウトエラーが発生しました。しばらく時間をおいてもう一度アクセスしてください。しばらく経ってもエラーが解消しない場合、管理者へご連絡ください。");
                    exit();
                }
                $page = [
                    "title"=>"パスワード再設定 完了 - ".SITE_NAME,
                    "current"=>0,
                    "auth"=>"reset_comp",
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
                $view->addTemplate(VIEW_DIR_USER."/user/auth/reset_comp.inc");
                $view->addTemplate(VIEW_DIR_USER."/footer_empty.inc");
                $view->display();
                exit();
            }
            $reset_err = $user_auth->reset_err();
        }else{
            $reset_err = implode("",$err);
        }
    }








    # View
    // $metas = [
    //     "title"=>"パスワード再設定 - ".SITE_NAME,
    //     "description"=>SITE_NAME."のパスワード再設定ページです。".META_DESCRIPTION,
    //     "keywords"=>META_KEYWORDS,
    //     "twitter:card"=>META_TWITTER_CARD,
    //     "twitter:site"=>META_TWITTER_ID,
    //     "twitter:creator"=>META_TWITTER_ID,
    //     "twitter:title"=>"パスワード再設定 - ".SITE_NAME,
    //     "twitter:description"=>SITE_NAME."のパスワード再設定ページです。".META_DESCRIPTION,
    // ];
    // $ogps = [
    //     "og:title"=>"パスワード再設定 - ".SITE_NAME,
    //     "og:description"=>SITE_NAME."のパスワード再設定ページです。".META_DESCRIPTION,
    //     "og:type"=>META_TYPE,
    //     "og:url"=>META_URL,
    //     "og:image"=>META_TWITTER_IMAGE,
    //     "og:site_name"=>META_SITE_NAME,
    //     "og:locale"=>META_LOCALE,
    //     "fb:app_id"=>META_FACEBOOK_APP_ID,
    // ];
    $page = [
        "title"=>"パスワード再設定 - ".SITE_NAME,
        "current"=>0,
        // "auth"=>"reset",
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
    $view->addParam("reset_err", isset($reset_err) ? $reset_err : null);
    $view->addParam("csrf_token", setToken());
    $view->addTemplate(VIEW_DIR_USER."/header.inc");
    $view->addTemplate(VIEW_DIR_USER."/user/auth/reset.inc");
    $view->addTemplate(VIEW_DIR_USER."/footer_empty.inc");
    $view->display();