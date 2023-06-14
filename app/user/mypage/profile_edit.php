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
        header("Location: /login/?r=".urlencode(HTTP_USER2.$_SERVER['REQUEST_URI']));
        exit();
    }else{
        $login_flg = true;
    }

    # プロフィール画像取得
    $icon_urls = getUserIcon($db,isset($login_flg) && $login_flg===true ? $user_auth->user_data()['user_id'] : null);
    $user_icon_url = $icon_urls[0];
    $user_icon_url_thumb = $icon_urls[1];

    # 変数定義
    $mode = null;
    $user_name = $user_auth->user_data()['user_name'];
    $user_key = $user_auth->user_detail_data()['user_key'];
    $nickname = $user_auth->user_detail_data()['user_nickname'];
    $err = [];
    $err['user_name'] = [];
    $err['user_key'] = [];
    $err['nickname'] = [];


    # 戻るフォームが送られてきたとき
    if(isset($_POST["mode"]) && $_POST["mode"] === "back"){

        # csrf対策
        $token = filter_input(INPUT_POST, 'csrf_token');
        if (!isset($_SESSION['csrf_token']) || $token!==$_SESSION['csrf_token']){
            displayUserErrorExit(400,"400 Bad Request","不正なアクセスです。");
        }
        unset($_SESSION['csrf_token']);

        # 値取得
        $mode = "edit";
        $user_name = filter_input(INPUT_POST, 'user_name');
        $user_key = filter_input(INPUT_POST, 'user_key');
        $nickname = filter_input(INPUT_POST, 'nickname');

        goto err;
    }






    # バリデーション
    if(isset($_POST["mode"]) && ($_POST["mode"] === "edit" || $_POST["mode"] === "confirm")){

        $mode = filter_input(INPUT_POST, 'mode');

        # csrf対策
        $token = filter_input(INPUT_POST, 'csrf_token');
        if (!isset($_SESSION['csrf_token']) || $token!==$_SESSION['csrf_token']){
            displayUserErrorExit(400,"400 Bad Request","不正なアクセスです。");
        }
        unset($_SESSION['csrf_token']);

        # 値取得
        $user_name = filter_input(INPUT_POST, 'user_name');
        $user_key = filter_input(INPUT_POST, 'user_key');
        $nickname = filter_input(INPUT_POST, 'nickname');

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
        if($u = $db->getUserDetailDataByKey($user_key)){
            if($u['user_key']!==$user_auth->user_detail_data()['user_key']){
                $err['user_key'][] = "指定されたユーザーIDはすでに使用されています。";
            }
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
            $edit_err = $err;
            $mode = "edit";
            goto err;
        }
    }








    # プロフィール編集フォームが送られてきたとき
    if(isset($_POST["mode"]) && $_POST["mode"] === "edit"){

        # 画像
        unset($_SESSION['image']);
        if($_POST["mode"]==="edit" && isset($_POST['default_icon']) && (int)$_POST['default_icon']===1){
            $_SESSION['image'] = [];
            $_SESSION['image']['img_default'] = "user_icon";
        }else{
            if(isset($_FILES['image']) && !(int)$_FILES['image']['error']){
                $_SESSION['image'] = [];
                $_SESSION['image']['img_content'] = file_get_contents($_FILES['image']['tmp_name']);
                $_SESSION['image']['img_name'] = $_FILES['image']['name'];
                $_SESSION['image']['img_type'] = exif_imagetype($_FILES['image']['tmp_name']);
                $_SESSION['image']['img_info'] = getimagesize($_FILES['image']['tmp_name']);
            }
        }


        $page = [
            "title"=>"プロフィール編集 確認 - ".SITE_NAME,
            "current"=>0,
            "auth"=>"edit",
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
        $view->addParam("edit_err", isset($edit_err) ? $edit_err : null);
        $view->addParam("csrf_token", setToken());
        $view->addTemplate(VIEW_DIR_USER."/header.inc");
        $view->addTemplate(VIEW_DIR_USER."/user/mypage/profile_edit.inc");
        $view->addTemplate(VIEW_DIR_USER."/footer_empty.inc");
        $view->display();
        exit();
    }




    # 確認フォームが送られてきたとき
    if(isset($_POST["mode"]) && $_POST["mode"] === "confirm"){
        
        $db->begin();
        
        # プロフィール編集
        if(!$user_auth->updateUser($db, $user_name, null, ["user_key"=>$user_key,"user_nickname"=>$nickname], false)){
            $db->rollback();
            sendErrorLog(500,"500 Internal Server Error","サーバーエラー発生。プロフィール編集に失敗しています。error_message：".$user_auth->edit_err());
            displayUserErrorExit(500,"500 Internal Server Error","プロフィール編集エラーが発生しました。しばらく時間をおいてもう一度アクセスしてください。しばらく経ってもエラーが解消しない場合、管理者へご連絡ください。");
        }

        # 画像登録
        if(isset($_SESSION['image'])){
            if(isset($_SESSION['image']['img_default']) && $_SESSION['image']['img_default']==="user_icon"){
                if(!deleteImageData($db,IMG_CATE_USER,IMG_SUBCATE_USER_ICON,$user_auth->user_data()['user_id'])){
                    $db->rollback();
                    sendErrorLog(500,"500 Internal Server Error","サーバーエラー発生。プロフィール編集の画像登録に失敗しています。error_message：".$db->getErr());
                    displayUserErrorExit(500,"500 Internal Server Error","プロフィール編集エラーが発生しました。しばらく時間をおいてもう一度アクセスしてください。しばらく経ってもエラーが解消しない場合、管理者へご連絡ください。");
                }
            }else{
                if(!deleteImageData($db,IMG_CATE_USER,IMG_SUBCATE_USER_ICON,$user_auth->user_data()['user_id'])){
                    $db->rollback();
                    sendErrorLog(500,"500 Internal Server Error","サーバーエラー発生。プロフィール編集の画像登録に失敗しています。error_message：".$db->getErr());
                    displayUserErrorExit(500,"500 Internal Server Error","プロフィール編集エラーが発生しました。しばらく時間をおいてもう一度アクセスしてください。しばらく経ってもエラーが解消しない場合、管理者へご連絡ください。");
                }
                if(!insertImageDataContent($db,$_SESSION['image']['img_content'],$_SESSION['image']['img_name'],$_SESSION['image']['img_type'],$_SESSION['image']['img_info'],IMG_CATE_USER,IMG_SUBCATE_USER_ICON,$user_auth->user_data()['user_id'])){
                    $db->rollback();
                    sendErrorLog(500,"500 Internal Server Error","サーバーエラー発生。プロフィール編集の画像登録に失敗しています。error_message：".$db->getErr());
                    displayUserErrorExit(500,"500 Internal Server Error","プロフィール編集エラーが発生しました。しばらく時間をおいてもう一度アクセスしてください。しばらく経ってもエラーが解消しない場合、管理者へご連絡ください。");
                };
            }
            unset($_SESSION['image']);
        }

        $db->insertUserLog("EDIT USER", ($user_auth->user_data()!==null ? $user_auth->user_data()['user_id'] : null));
        $db->commit();
        
        header("Location: /mypage/");
        exit();
    }



    err:



    # View
    $metas = [
        "title"=>"プロフィール編集 - ".SITE_NAME,
        "description"=>SITE_NAME."のプロフィール編集ページです。".META_DESCRIPTION,
        "keywords"=>META_KEYWORDS,
        "twitter:card"=>META_TWITTER_CARD,
        "twitter:site"=>META_TWITTER_ID,
        "twitter:creator"=>META_TWITTER_ID,
        "twitter:title"=>"プロフィール編集 - ".SITE_NAME,
        "twitter:description"=>SITE_NAME."のプロフィール編集ページです。".META_DESCRIPTION,
    ];
    $ogps = [
        "og:title"=>"プロフィール編集 - ".SITE_NAME,
        "og:description"=>SITE_NAME."のプロフィール編集ページです。".META_DESCRIPTION,
        "og:type"=>META_TYPE,
        "og:url"=>META_URL,
        "og:image"=>META_TWITTER_IMAGE,
        "og:site_name"=>META_SITE_NAME,
        "og:locale"=>META_LOCALE,
        "fb:app_id"=>META_FACEBOOK_APP_ID,
    ];
    $page = [
        "title"=>"プロフィール編集 - ".SITE_NAME,
        "current"=>0,
        "login_flg"=>$login_flg,
        "user_data"=>$user_auth->user_data()!==null ? $user_auth->user_data() : null,
        "user_detail_data"=>$user_auth->user_detail_data()!==null ? $user_auth->user_detail_data() : null,
        "user_icon_url"=>$user_icon_url,
        "user_icon_url_thumb"=>$user_icon_url_thumb,
        "head_css"=>["/assets/css/signin.css"],
        "head_js"=>["/assets/js/JSValidation.js","/assets/js/file_input.js"],
        "foot_js"=>[]
    ];
    $view = new View();
    $view->addParam("metas", $metas);
    $view->addParam("ogps", $ogps);
    $view->addParam("page", $page);
    $view->addParam("mode", isset($mode) ? $mode : "edit");
    $view->addParam("user_name", isset($user_name) ? $user_name : "");
    $view->addParam("user_key", isset($user_key) ? $user_key : "");
    $view->addParam("nickname", isset($nickname) ? $nickname : "");
    $view->addParam("edit_err", isset($edit_err) ? $edit_err : null);
    $view->addParam("csrf_token", setToken());
    $view->addTemplate(VIEW_DIR_USER."/header.inc");
    $view->addTemplate(VIEW_DIR_USER."/user/mypage/profile_edit.inc");
    $view->addTemplate(VIEW_DIR_USER."/footer_empty.inc");
    $view->display();