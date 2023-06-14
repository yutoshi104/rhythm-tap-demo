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

    # メッセージがあれば表示
    if(isset($_SESSION['login_msg'])){
        $login_msg = $_SESSION['login_msg'];
        unset($_SESSION['login_msg']);
    }

    # エラーがあれば表示
    if(isset($_SESSION['login_err'])){
        $login_err = $_SESSION['login_err'];
        unset($_SESSION['login_err']);
    }

    # ログインフォームが送られてきたとき
    if(isset($_POST["mode"]) && $_POST["mode"] === "login"){
        # csrf対策
        $token = filter_input(INPUT_POST, 'csrf_token');
        if (!isset($_SESSION['csrf_token']) || $token!==$_SESSION['csrf_token']){
            displayUserErrorExit(400,"400 Bad Request","不正なアクセスです。");
        }
        unset($_SESSION['csrf_token']);

        # 値取得
        $email = filter_input(INPUT_POST, 'email');
        $password = filter_input(INPUT_POST, 'password');

        # バリデーション
        $err = [];
        if(!isValid($email)){
            $err[] = "メールアドレスが入力されていません。";
        }
        if(!isEmail($email)){
            $err[] = "正しいメールアドレスではありません。";
        }
        if(!isValid($password)){
            $err[] = "パスワードが入力されていません。";
        }

        if(!$err){
            if($user_auth->loginUser($db, $email, $password)){
                $db->insertUserLog("LOGIN USER", ($user_auth->user_data()!==null ? $user_auth->user_data()['user_id'] : null));
                // トップページへリダイレクト(あるいはページバック)
                if(isset($_GET['r'])){
                    header("Location: ".urldecode($_GET['r']));
                    exit();
                }else{
                    header("Location: /");
                    exit();
                }
            }
            $login_err = $user_auth->login_err();
        }else{
            $login_err = implode("",$err);
        }
    }







    # View
    $metas = [
        "title"=>"ログイン - ".SITE_NAME,
        "description"=>SITE_NAME."のログインページです。".META_DESCRIPTION,
        "keywords"=>META_KEYWORDS,
        "twitter:card"=>META_TWITTER_CARD,
        "twitter:site"=>META_TWITTER_ID,
        "twitter:creator"=>META_TWITTER_ID,
        "twitter:title"=>"ログイン - ".SITE_NAME,
        "twitter:description"=>SITE_NAME."のログインページです。".META_DESCRIPTION,
    ];
    $ogps = [
        "og:title"=>"ログイン - ".SITE_NAME,
        "og:description"=>SITE_NAME."のログインページです。".META_DESCRIPTION,
        "og:type"=>META_TYPE,
        "og:url"=>META_URL,
        "og:image"=>META_TWITTER_IMAGE,
        "og:site_name"=>META_SITE_NAME,
        "og:locale"=>META_LOCALE,
        "fb:app_id"=>META_FACEBOOK_APP_ID,
    ];
    $page = [
        "title"=>"ログイン - ".SITE_NAME,
        "current"=>0,
        "auth"=>"login",
        "user_data"=>$user_auth->user_data()!==null ? $user_auth->user_data() : null,
        "user_detail_data"=>$user_auth->user_detail_data()!==null ? $user_auth->user_detail_data() : null,
        "head_css"=>["/assets/css/signin.css"],
        "head_js"=>[],
        "foot_js"=>[]
    ];
    $view = new View();
    $view->addParam("metas", $metas);
    $view->addParam("ogps", $ogps);
    $view->addParam("page", $page);
    $view->addParam("login_msg", isset($login_msg) ? $login_msg : null);
    $view->addParam("login_err", isset($login_err) ? $login_err : null);
    $view->addParam("csrf_token", setToken());
    $view->addTemplate(VIEW_DIR_USER."/header.inc");
    $view->addTemplate(VIEW_DIR_USER."/user/auth/login.inc");
    $view->addTemplate(VIEW_DIR_USER."/footer_empty.inc");
    $view->display();