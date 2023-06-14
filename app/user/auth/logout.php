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

    # login session
    $user_auth = new UserAuth();
    if(!$user_auth->authSession($db)){
        $login_flg = false;
    }else{
        $login_flg = true;
    }

    # プロフィール画像取得
    $icon_urls = getUserIcon($db, isset($login_flg) && $login_flg===true ? $user_auth->user_data()['user_id'] : null);
    $user_icon_url = $icon_urls[0];
    $user_icon_url_thumb = $icon_urls[1];

    # logout session
    if(!$user_auth->logoutUser($db)){
        displayUserErrorExit(500,"500 Internal Server Error","ログアウトエラーが発生しました。しばらく時間をおいてもう一度アクセスしてください。しばらく経ってもエラーが解消しない場合、管理者へご連絡ください。");
        exit();
    }
    session_destroy();
    $db->insertUserLog("LOGOUT USER", ($user_auth->user_data()!==null ? $user_auth->user_data()['user_id'] : null));
    session_start();
    session_regenerate_id(true);
    $_SESSION['login_msg'] = "ログアウトしました。";
    header("Location: /login/");
    exit();