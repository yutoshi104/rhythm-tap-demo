<?php

# ディレクトリ
define("BASE_DIR", "/var/www/html/yutoshi.com/portfolio");

# エラー表示させない
ini_set('display_errors', 0);

# 各種設定
define("DOMAIN", "portfolio.yutoshi.com");
define("PROTOCOL","https");
define("HTTP_ADMIN", sprintf("%s://%s/yt-admin/", PROTOCOL, DOMAIN));
define("HTTP_USER", sprintf("%s://%s/", PROTOCOL, DOMAIN));
define("HTTP_USER2", sprintf("%s://%s", PROTOCOL, DOMAIN));

# DB
define("DB_HOST_MASTER", "127.0.0.1");
define("DB_HOST_SLAVE", "127.0.0.1");
define("DB_PORT_MASTER", 3306);
define("DB_PORT_SLAVE", 3306);
define("DB_USER", "yuto_portfolio");
define("DB_PASSWORD", "e7iWO[CGAYkKeefx");
define("DB_NAME", "portfolio_db");
define("DB_CHARSET", "utf8mb4");
define("DB_TIMEOUT", 5);

# redis
define("REDIS_INFO", [
	"host" => '127.0.0.1',
	"port" => 6379,
]);
define("REDIS_PREFIX", "portfolio_");

# メールアドレス
define("FROM_EMAIL","noreply@yutoshi.com");
define("REPORT_FROM_EMAIL","noreply@yutoshi.com");
define("REPORT_TO_EMAIL","nagoya.yuto.0925@gmail.com");

# SNS情報
define("TWITTER_USERNAME","yutoshi_we");
define("INSTAGRAM_USERNAME","yutoshi104");
define("FACEBOOK_ID","100011140366603");
define("GITHUB_ID","yutoshi104");
define("LINKEDIN_ID","yutoshi");

# google recaptcha
define("G_RECAPTCHA_SITE_KEY","");
define("G_RECAPTCHA_SECRET_KEY","");
