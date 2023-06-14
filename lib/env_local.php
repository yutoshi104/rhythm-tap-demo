<?php

# ディレクトリ
define("BASE_DIR", "/var/www/html");

# エラー表示させない
// ini_set('display_errors', 0);

# メール送信
define("NO_SEND_MAIL", true);

# 各種設定
define("DOMAIN", "localhost");
define("PROTOCOL","http");
define("HTTP_ADMIN", sprintf("%s://%s/yt-admin/", PROTOCOL, DOMAIN));
define("HTTP_USER", sprintf("%s://%s/", PROTOCOL, DOMAIN));
define("HTTP_USER2", sprintf("%s://%s", PROTOCOL, DOMAIN));

# DB
define("DB_HOST_MASTER", "localhost");
define("DB_HOST_SLAVE", "localhost");
define("DB_PORT_MASTER", 1243);
define("DB_PORT_SLAVE", 1243);
define("DB_USER", "test");
define("DB_PASSWORD", "test");
define("DB_NAME", "rhythm_tap_demo_db");
define("DB_CHARSET", "utf8mb4");
define("DB_TIMEOUT", 5);

# redis
define("REDIS_INFO", [
	"host" => '127.0.0.1',
	"port" => 1343,
]);
define("REDIS_PREFIX", "rhythm_tap_demo_");

# メールアドレス
define("FROM_EMAIL","nagoya.yuto.0925@gmail.com");
define("REPORT_FROM_EMAIL","nagoya.yuto.0925@gmail.com");
define("REPORT_TO_EMAIL","nagoya.yuto.0925@gmail.com");

# SNS情報
define("TWITTER_USERNAME","yutoshi_we");
define("INSTAGRAM_USERNAME","yutoshi104");
define("FACEBOOK_ID","100011140366603");
define("GITHUB_ID","yutoshi104");
define("LINKEDIN_ID","yutoshi");

# google recaptcha
// define("G_RECAPTCHA_NOCHECK",true);
define("G_RECAPTCHA_SITE_KEY","6LfmP18lAAAAAL1-nikZzqCM34nGpGssCmgCVvlm");
define("G_RECAPTCHA_SECRET_KEY","6LfmP18lAAAAABBAR_eiiBULVZRT0s8WYX4F6vJd");
