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
        // 今回はログインページにリダイレクト
        $_SESSION['login_err'] = "ログインされていません。ログインしてください。";
        header("Location: /login/?r=".urlencode($_SERVER['REQUEST_URI']));
        exit();
    }else{
        $login_flg = true;
    }

    # プロフィール画像取得
    $icon_urls = getUserIcon($db,isset($login_flg) && $login_flg===true ? $user_auth->user_data()['user_id'] : null);
    $user_icon_url = $icon_urls[0];
    $user_icon_url_thumb = $icon_urls[1];

    # user_key取得
    if(isset($_SESSION['key'])){
        $user_key = $_SESSION['key'];
        unset($_SESSION['key']);
    }else if($login_flg){
        $user_key = $user_auth->user_detail_data()['user_key'];
    }else{
        displayUserErrorExit(400,"400 Bad Request","不正なアクセスです。");
    }

    # マイページかの判定 & データ取得
    $data = [];
    if($user_auth->user_detail_data()!==null && $user_key===$user_auth->user_detail_data()['user_key']){
        $mypage_flg = true;
        $data = array_merge($user_auth->user_detail_data(), $user_auth->user_data());
    }else{
        if($data = $db->getUserUserDetailDataByKeyNotDelf($user_key)){
            $mypage_flg = false;
        }else{
            displayUserErrorExit(404,"404 Not Found","ご指定のページは存在しません。");
        }
    }

    # 表示ユーザープロフィール画像取得
    $disp_icon_urls = getUserIcon($db,$data['user_id']);
    $disp_user_icon_url = $icon_urls[0];
    $disp_user_icon_url_thumb = $icon_urls[1];





    # 参加しているグループリスト(ログイン時)
    $user_group_list = [];
    if($mypage_flg){
        $user_group_member_list = $db->getGroupMemberFromUserIdNotDelfNotStatus($user_auth->user_data()['user_id']);
        if($user_group_member_list===null){
            $user_group_member_list = [];
        }
        foreach($user_group_member_list as $ugml){
            $gd = getBattleData($db,$ugml['group_key']);
            if($gd !== null){
                $user_group_list[] = $gd;
            }
        }
    }

// print_r($data);
// exit();



error_log(hideEmail(h($data['user_email'])));


    # View
    $metas = [
        "title"=>$data['user_nickname']."のマイページ - ".SITE_NAME,
        "description"=>$data['user_nickname']."のマイページです。".META_DESCRIPTION,
        "keywords"=>META_KEYWORDS,
        "twitter:card"=>META_TWITTER_CARD,
        "twitter:site"=>META_TWITTER_ID,
        "twitter:creator"=>META_TWITTER_ID,
        "twitter:title"=>$data['user_nickname']."のマイページ - ".SITE_NAME,
        "twitter:description"=>$data['user_nickname']."のマイページです。".META_DESCRIPTION,
    ];
    $ogps = [
        "og:title"=>$data['user_nickname']."のマイページ - ".SITE_NAME,
        "og:description"=>$data['user_nickname']."のマイページです。".META_DESCRIPTION,
        "og:type"=>META_TYPE,
        "og:url"=>META_URL,
        "og:image"=>META_TWITTER_IMAGE,
        "og:site_name"=>META_SITE_NAME,
        "og:locale"=>META_LOCALE,
        "fb:app_id"=>META_FACEBOOK_APP_ID,
    ];
    $page = [
        "title"=>$data['user_nickname']."のマイページ - ".SITE_NAME,
        "current"=>5,
        "login_flg"=>$login_flg,
        "user_data"=>$user_auth->user_data()!==null ? $user_auth->user_data() : null,
        "user_detail_data"=>$user_auth->user_detail_data()!==null ? $user_auth->user_detail_data() : null,
        "user_icon_url"=>$user_icon_url,
        "user_icon_url_thumb"=>$user_icon_url_thumb,
        "head_css"=>[],
        "head_js"=>[],
        "foot_js"=>[]
    ];
    $view = new View();
    $view->addParam("metas", $metas);
    $view->addParam("ogps", $ogps);
    $view->addParam("page", $page);
    $view->addParam("mypage_flg", $mypage_flg);
    $view->addParam("data", $data);
    $view->addParam("disp_user_icon_url", $disp_user_icon_url);
    $view->addParam("disp_user_icon_url_thumb", $disp_user_icon_url_thumb);
    $view->addParam("user_group_list", $user_group_list);
    $view->addParam("csrf_token", setToken());
    $view->addTemplate(VIEW_DIR_USER."/header.inc");
    $view->addTemplate(VIEW_DIR_USER."/user/mypage/mypage.inc");
    $view->addTemplate(VIEW_DIR_USER."/footer.inc");
    $view->display();