<?php


    # include
    $lib_dir = __DIR__."/../../lib";
    require_once($lib_dir."/common_inc.php");

    # DB connect
    $db = new Db();
    if( $db->init_master() === false ) {
        sendErrorLog(500,"500 Internal Server Error","サーバーエラー発生。DBの接続に失敗しています。error_message：".$db->getErr());
        displayUserErrorExit(500,"500 Internal Server Error","サーバーエラーが発生しています。しばらく時間をおいてもう一度アクセスしてください。しばらく経ってもエラーが解消しない場合、管理者へご連絡ください。");
    }


    # View
    $metas = [
        "title"=>META_TITLE,
        "description"=>META_DESCRIPTION,
        "keywords"=>META_KEYWORDS,
        "twitter:card"=>META_TWITTER_CARD,
        "twitter:site"=>META_TWITTER_ID,
        "twitter:creator"=>META_TWITTER_ID,
        "twitter:title"=>META_TITLE,
        "twitter:description"=>META_DESCRIPTION,
    ];
    $ogps = [
        "og:title"=>META_TITLE,
        "og:description"=>META_DESCRIPTION,
        "og:type"=>META_TYPE,
        "og:url"=>META_URL,
        "og:image"=>META_TWITTER_IMAGE,
        "og:site_name"=>META_SITE_NAME,
        "og:locale"=>META_LOCALE,
        "fb:app_id"=>META_FACEBOOK_APP_ID,
    ];
    $page = [
        "title"=>META_TITLE,
        "current"=>1,
        "current2"=>1,
        "head_css"=>[],
        "head_js"=>[],
        "foot_js"=>[]
    ];

    $view = new View();
    $view->addParam("metas", $metas);
    $view->addParam("ogps", $ogps);
    $view->addParam("page", $page);

    $view->addTemplate(VIEW_DIR_USER."/header.inc");
    $view->addTemplate(VIEW_DIR_USER."/top.inc");
    $view->addTemplate(VIEW_DIR_USER."/footer.inc");
    $view->display();









