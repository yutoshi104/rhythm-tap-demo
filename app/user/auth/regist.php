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
    if($user_auth->authSession($db)){
        if((int)$user_auth->user_data()['status'] === 0){
            // リダイレクト(ログイン済み)
            header("Location: /");
            exit();
        }
    }

    # email_sessionの確認
    $email_session_id = isset($_SESSION['psid']) ? $_SESSION['psid'] : null;
    if(!$email = $user_auth->authEmailSession($db,$email_session_id)){
        displayUserErrorExit(400,"400 Bad Request","セッションの有効期限が切れています。お手数ですが、もう一度登録をやり直してください。");
    }

    # 変数定義
    $mode = null;
    $user_name = null;
    $user_key = null;
    $nickname = null;
    $password = null;
    $password_conf = null;
    $err = [];
    $err['user_name'] = [];
    $err['user_key'] = [];
    $err['nickname'] = [];
    $err['email'] = [];
    $err['password'] = [];
    $err['recaptcha'] = [];




    # 戻るフォームが送られてきたとき
    if(isset($_POST["mode"]) && $_POST["mode"] === "back"){

        # csrf対策
        $token = filter_input(INPUT_POST, 'csrf_token');
        if (!isset($_SESSION['csrf_token']) || $token!==$_SESSION['csrf_token']){
            displayUserErrorExit(400,"400 Bad Request","不正なアクセスです。");
        }
        unset($_SESSION['csrf_token']);

        # 値取得
        $mode = "regist";
        $user_name = filter_input(INPUT_POST, 'user_name');
        $user_key = filter_input(INPUT_POST, 'user_key');
        $nickname = filter_input(INPUT_POST, 'nickname');

        goto err;
    }






    # バリデーション
    if(isset($_POST["mode"]) && ($_POST["mode"] === "regist" || $_POST["mode"] === "confirm")){

        $mode = filter_input(INPUT_POST, 'mode');

        # csrf対策
        $token = filter_input(INPUT_POST, 'csrf_token');
        if (!isset($_SESSION['csrf_token']) || $token!==$_SESSION['csrf_token']){
            displayUserErrorExit(400,"400 Bad Request","不正なアクセスです。");
        }
        unset($_SESSION['csrf_token']);

        # google recaptcha
        if($mode === 'regist'){
            if(!$recaptcha_result = checkRecaptcha()){
                $err['recaptcha'][] = "reCAPTCHAにチェックを入れてください。";
            }else{
                $_SESSION['recaptcha'] = $recaptcha_result;
            }
        }else{
            if(!isset($_SESSION['recaptcha']) || !$_SESSION['recaptcha']){
                displayUserErrorExit(400,"400 Bad Request","不正なアクセスです。");
            }
            unset($_SESSION['recaptcha']);
        }

        # 値取得
        $user_name = filter_input(INPUT_POST, 'user_name');
        $user_key = filter_input(INPUT_POST, 'user_key');
        $nickname = filter_input(INPUT_POST, 'nickname');
        $password = filter_input(INPUT_POST, 'password');
        $password_conf = filter_input(INPUT_POST, 'password_conf');

        # 入力チェック
        if(!isValid($user_name)){
            $err['user_name'][] = "フルネームが入力されていません。";
        }
        if(!isValidStrLen($user_name,1,MAX_NAME)){
            $err['user_name'][] = "フルネームは".MAX_NAME."文字以内にしてください。";
        }
        if(!isValid($nickname)){
            $err['nickname'][] = "ニックネームが入力されていません。";
        }
        if(!isValidStrLen($nickname,1,MAX_NICKNAME)){
            $err['nickname'][] = "ニックネームは".MAX_NICKNAME."文字以内にしてください。";
        }
        if(!isValid($user_key)){
            $err['user_key'][] = "ユーザーIDが入力されていません。";
        }
        if(!isUserKey($user_key)){
            $err['user_key'][] = "ユーザーIDは半角英数字と「_」のみ使用可能です。";
        }
        if(!isValidStrLen($user_key,1,MAX_USERKEY)){
            $err['user_key'][] = "ユーザーIDは".MAX_USERKEY."文字以内にしてください。";
        }
        if($db->getUserDetailDataByKey($user_key)){
            $err['user_key'][] = "指定されたユーザーIDはすでに使用されています。";
        }
        if(!isValid($password)){
            $err['password'][] = "パスワードが入力されていません。";
        }
        if(!isPassword($password)){
            $err['password'][] = "パスワードには少なくとも英数をそれぞれ1文字以上含めてください。";
        }
        if(!isValidStrLen($password,MIN_PASSWORD,MAX_PASSWORD)){
            $err['password'][] = "パスワードは".MIN_PASSWORD."文字以上".MAX_PASSWORD."文字以内にしてください。";
        }
        if($mode==="regist" && $password!==$password_conf){
            $err['password'][] = "パスワード再入力が一致しません。";
        }

        # エラー存在チェック
        $err_flg = false;
        foreach($err as $e){
            if(!empty($e)){
                $err_flg = true;
                break;
            }
        }
        if($err_flg){
            $regist_err = $err;
            $mode = "regist";
            unset($_SESSION['recaptcha']);
            goto err;
        }
    }








    # 新規登録フォームが送られてきたとき
    if(isset($_POST["mode"]) && $_POST["mode"] === "regist"){

        # 画像
        if(isset($_FILES['image']) && !(int)$_FILES['image']['error']){
            $_SESSION['image'] = [];
            $_SESSION['image']['img_content'] = file_get_contents($_FILES['image']['tmp_name']);
            $_SESSION['image']['img_name'] = $_FILES['image']['name'];
            $_SESSION['image']['img_type'] = exif_imagetype($_FILES['image']['tmp_name']);
            $_SESSION['image']['img_info'] = getimagesize($_FILES['image']['tmp_name']);
        }else{
            $_SESSION['image'] = [];
            $_SESSION['image']['img_default'] = "user_icon";
        }

        $page = [
            "title"=>"新規登録 確認 - ".SITE_NAME,
            "current"=>0,
            "auth"=>"regist",
            "user_data"=>$user_auth->user_data()!==null ? $user_auth->user_data() : null,
            "user_detail_data"=>$user_auth->user_detail_data()!==null ? $user_auth->user_detail_data() : null,
            "head_css"=>["/assets/css/signin.css"],
            "head_js"=>[],
            "foot_js"=>[]
        ];
        $view = new View();
        $view->addParam("page", $page);
        $view->addParam("mode", "confirm");
        $view->addParam("user_name", isset($user_name) ? $user_name : "");
        $view->addParam("user_key", isset($user_key) ? $user_key : "");
        $view->addParam("nickname", isset($nickname) ? $nickname : "");
        $view->addParam("email", isset($email) ? $email : "");
        $view->addParam("password", isset($password) ? $password : "");
        $view->addParam("regist_err", isset($regist_err) ? $regist_err : null);
        $view->addParam("csrf_token", setToken());
        $view->addTemplate(VIEW_DIR_USER."/header.inc");
        $view->addTemplate(VIEW_DIR_USER."/user/auth/regist.inc");
        $view->addTemplate(VIEW_DIR_USER."/footer_empty.inc");
        $view->display();
        exit();
    }




    # 確認フォームが送られてきたとき
    if(isset($_POST["mode"]) && $_POST["mode"] === "confirm"){
        
        $db->begin();
        
        # ユーザー登録
        if(!$user_id = $user_auth->registUser($db, $user_name, $email, $password, 0, ["user_key"=>$user_key,"user_nickname"=>$nickname,"guest_flg"=>0], false)){
            $db->rollback();
            sendErrorLog(500,"500 Internal Server Error","サーバーエラー発生。新規登録に失敗しています。error_message：".$user_auth->regist_err());
            displayUserErrorExit(500,"500 Internal Server Error","登録エラーが発生しました。しばらく時間をおいてもう一度アクセスしてください。しばらく経ってもエラーが解消しない場合、管理者へご連絡ください。");
        }

        # 画像登録
        if(isset($_SESSION['image'])){
            if(isset($_SESSION['image']['img_default']) && $_SESSION['image']['img_default']==="user_icon"){
            }else{
                if(!insertImageDataContent($db,$_SESSION['image']['img_content'],$_SESSION['image']['img_name'],$_SESSION['image']['img_type'],$_SESSION['image']['img_info'],IMG_CATE_USER,IMG_SUBCATE_USER_ICON,$user_id)){
                    $db->rollback();
                    sendErrorLog(500,"500 Internal Server Error","サーバーエラー発生。新規登録の画像登録に失敗しています。error_message：".$db->getErr());
                    displayUserErrorExit(500,"500 Internal Server Error","登録エラーが発生しました。しばらく時間をおいてもう一度アクセスしてください。しばらく経ってもエラーが解消しない場合、管理者へご連絡ください。");
                };
                unset($_SESSION['image']);
            }
        }

        $db->insertUserLog("REGIST USER", ($user_auth->user_data()!==null ? $user_auth->user_data()['user_id'] : null));
        $db->commit();
        
        $page = [
            "title"=>"新規登録 完了 - ".SITE_NAME,
            "current"=>0,
            "login_flg"=>false,
            "user_data"=>$user_auth->user_data()!==null ? $user_auth->user_data() : null,
            "user_detail_data"=>$user_auth->user_detail_data()!==null ? $user_auth->user_detail_data() : null,
            "head_css"=>["/assets/css/signin.css"],
            "head_js"=>[],
            "foot_js"=>[]
        ];
        $view = new View();
        $view->addParam("page", $page);
        $view->addTemplate(VIEW_DIR_USER."/header.inc");
        $view->addTemplate(VIEW_DIR_USER."/user/auth/regist_comp.inc");
        $view->addTemplate(VIEW_DIR_USER."/footer.inc");
        $view->display();
        exit();
    }



    err:



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
        "head_css"=>["/assets/css/signin.css"],
        "head_js"=>["/assets/js/JSValidation.js","/assets/js/file_input.js"],
        "foot_js"=>[]
    ];
    $view = new View();
    $view->addParam("metas", $metas);
    $view->addParam("ogps", $ogps);
    $view->addParam("page", $page);
    $view->addParam("mode", isset($mode) ? $mode : "regist");
    $view->addParam("user_name", isset($user_name) ? $user_name : "");
    $view->addParam("user_key", isset($user_key) ? $user_key : "");
    $view->addParam("nickname", isset($nickname) ? $nickname : "");
    $view->addParam("email", isset($email) ? $email : "");
    $view->addParam("regist_err", isset($regist_err) ? $regist_err : null);
    $view->addParam("csrf_token", setToken());
    $view->addTemplate(VIEW_DIR_USER."/header.inc");
    $view->addTemplate(VIEW_DIR_USER."/user/auth/regist.inc");
    $view->addTemplate(VIEW_DIR_USER."/footer_empty.inc");
    $view->display();