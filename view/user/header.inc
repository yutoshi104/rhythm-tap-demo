<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO -->
    <?php
        if(isset($metas)){
            foreach($metas as $name=>$content){
                if(isset($name) && $name!==""){
                    printf('<meta name="%s" content="%s">'."\n", h($name), h($content));
                }
            }
        }
        if(isset($ogps)){
            foreach($ogps as $property=>$content){
                if(isset($property) && $property!==""){
                    printf('<meta property="%s" content="%s">'."\n", h($property), h($content));
                }
            }
        }
    ?>

    <!-- Google AdSense -->

    <!-- Global site tag (gtag.js) - Google Analytics -->

    <!-- PWA iPhone表示 -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="<?php echo META_TITLE; ?>">
    <link rel="apple-touch-icon" href="/assets/src/iphone_icon_user.png">
    <link rel="manifest" href="/manifest.json">

    <!-- import -->
    <link rel="icon" type="image/png" href="/assets/src/y_user.png">
    <link rel="stylesheet" href="/assets/css/common.css">
    <?php
        if(isset($page['head_css']) && !empty($page['head_css'])){
            foreach($page['head_css'] as $v){
                printf('<link rel="stylesheet" href="%s">'."\n", $v);
            }
        }
        if(isset($page['head_js']) && !empty($page['head_js'])){
            foreach($page['head_js'] as $v){
                printf('<script src="%s"></script>'."\n", $v);
            }
        }
    ?>

    <!-- ServiceWorker -->
    <!--
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service_worker.js').then(function(registration) {
                console.log('ServiceWorker registration successful with scope: ', registration.scope);
            }).catch(function(err) {
                console.log('ServiceWorker registration failed: ', err);
            });
        }
    </script>
    -->

    <title><?php echo $page['title']; ?></title>
</head>
<body>
    <header>
        <nav>
            <a class="menu-brand menu-link" href="/">
                <h1>Logo</h1>
            </a>
            <button id="humberger_button" type="button" class="humberger" aria-controls="navigation" aria-expanded="false" aria-label="menu">
                <span class="humberger-line"></span>
            </button>
            <div class="humberger-menu">
                <div class="humberger-menu-container">
                    <ul class="menu-ul">
                        <li class="menu-item<?php if($page['current']===1 && $page['current2']===1) echo ' active'; ?>">
                            <a class="menu-link" href="/#">TOP</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div id="loading" class="loading">
        loading...
    </div>