<?php

# 環境定義ファイル
require_once(__DIR__."/env_local.php");

# エンコーディング
mb_internal_encoding("UTF-8");
# タイムゾーン
date_default_timezone_set('Asia/Tokyo');


# ディレクトリ
define("ETC_DIR", BASE_DIR."/etc");
define("LIB_DIR", BASE_DIR."/lib");
define("PUBLIC_DIR", BASE_DIR."/public");
define("UPLOAD_DIR", PUBLIC_DIR."/upload");
define("IMG_SAVE_DIR", PUBLIC_DIR."/assets/img");
define("IMG_SAVE_REL_DIR", "/assets/img");
define("VIEW_DIR", BASE_DIR."/view");
define("VIEW_DIR_USER", VIEW_DIR."/user");
define("VIEW_DIR_ADMIN", VIEW_DIR."/admin");

# cookie
define("COOKIE_PREFIX", "ypw_");
define("COOKIE_NAME_ADMIN_LOGIN", COOKIE_PREFIX."asid");
define("COOKIE_NAME_USER_LOGIN", COOKIE_PREFIX."sid");

# ログイン
# login
define("LOGIN_ADMIN_SESSION_TIME", 36000); // 操作してない時間が10時間経ったらきれる
define("LOGIN_USER_SESSION_TIME", 36000);
# forgot
define("FORGOT_ADMIN_SESSION_TIME", 300);
define("FORGOT_USER_SESSION_TIME", 300);
# email_auth
define("EMAIL_ADMIN_SESSION_TIME", 1800);
define("EMAIL_USER_SESSION_TIME", 1800);

# コピーライト
define("COPYRIGHT", sprintf("Copyright&copy; %s Yuto All Right Reserved.", date("Y")));
define("COPYRIGHT_EMAIL", sprintf("Copyright %s Yuto All Right Reserved.", date("Y")));

# サイト名
define("SITE_NAME","Takase Rhythm Tap");

# SEO対策
define("META_TITLE", "Yutoshi Website");
define("META_DESCRIPTION", "Yutoのポートフォリオサイトです。趣味としてWeb開発しながらいろいろ勉強中の".floor((date("Ymd")-date("19990925"))/10000)."歳大学生です。 インターンでもエンジニアとしてWeb開発をしていて、大学の研究ではディープラーニングの研究なんかもやってます。");
define("META_KEYWORDS", "ポートフォリオ, Yuto, ゆうと, ユウト, portfolio, 学生, エンジニア, ブログ");
define("META_TYPE", "website");
define("META_URL", HTTP_USER);
define("META_LOCALE", "ja_JP");
define("META_SITE_NAME", "Yutoshi Website");
define("META_TWITTER_ID", "@".TWITTER_USERNAME);
define("META_TWITTER_CARD", "summary_large_image");
define("META_TWITTER_IMAGE", HTTP_USER2."/assets/src/twitter_card.png");
define("META_INSTAGRAM_ID", INSTAGRAM_USERNAME);
define("META_GITHUB_ID", GITHUB_ID);
define("META_FACEBOOK_APP_ID", "1853166861499601");




####################################################################################################
# upload
####################################################################################################

# ファイルアップロード
define("UPLOAD_IMAGE_DIR", UPLOAD_DIR."/img");
define("UPLOAD_FILE_DIR", UPLOAD_DIR."/file");
define("UPLOAD_TMP_DIR", UPLOAD_DIR."/tmp");
# 画像の種類
define("IMG_TYPE_LIST", [
	IMAGETYPE_GIF => "gif",
	IMAGETYPE_JPEG => "jpeg",
	IMAGETYPE_PNG => "png",
]);
# ファイルの種類
define("FILE_TYPE_LIST", [
	"image/gif",
	"image/jpeg",
	"image/png",
]);
# カテゴリー定義
define("IMG_CATE_USER", 1);
define("IMG_CATE_ADMIN", 2);
define("IMG_SUBCATE_USER_ICON", 1);
define("IMG_SUBCATE_ADMIN_ICON", 1);

####################################################################################################



####################################################################################################
# ユーザー
####################################################################################################

# ユーザーアイコン
define("USER_ICON_DEFAULT", "/assets/src/user_icon_default.png");

# 退会理由
define("LEAVE_REASON",[
    1 => "他のサービスの方が魅力的だった",
    2 => "使い方がわからなかった",
    3 => "操作がしづらかった",
    4 => "役立たなかった",
    5 => "利用に飽きてしまった",
    6 => "このサービスによって不快な思いをした",
    7 => "対人トラブルがあった",
    8 => "広告が多すぎる",
    // 9 => "",
    // 10 => "",
    // 11 => "",
    100 => "その他",
]);

# 各種制限
define("MAX_NAME",32);
define("MAX_NICKNAME",20);
define("MAX_USERKEY",50);
define("MIN_PASSWORD",8);
define("MAX_PASSWORD",32);

####################################################################################################



####################################################################################################
# 管理者
####################################################################################################

# 管理者権限
define("ADMIN_POSITION",[
    0 => "ノーマル管理者",      // 管理者管理以外の権限付与
    1 => "スーパー管理者",      // 全権付与
    2 => "ログ管理者",          // ログ管理のみの権限付与
    3 => "問い合わせ管理者",    // 問い合わせ管理のみの権限付与
    4 => "ユーザー管理者",      // ユーザー管理のみの権限付与
    5 => "退会アンケート管理者",           // 退会アンケート管理のみの権限付与
]);
# 管理者権限説明
define("ADMIN_POSITION_DESCRIPTION",[
    0 => "一般の管理権限(ログ管理、問い合わせ管理、ユーザー管理、退会アンケート管理)",
    1 => "全権",
    2 => "ログ管理の権限",
    3 => "問い合わせ管理の権限",
    4 => "ユーザー管理の権限",
    5 => "退会アンケート管理の権限",
]);
# 管理者権限
define("ADMIN_POSITION_COLOR",[
    0 => "success",
    1 => "danger",
    2 => "secondary",
    3 => "secondary",
    4 => "secondary",
    5 => "secondary",
]);

# ページング
define("ADMIN_PAGING_CHECK",20);

# ソート(管理者)
define("ADMIN_SORT_PATERN",[
    0 => [["intime","DESC"],"登録時間の新しい順"],
    1 => [["intime","ASC"],"登録時間の古い順"],
    // 2 => [[["checked","DESC"],["intime","DESC"]],"チェック済み優先"],
    // 3 => [[["checked","ASC"],["intime","DESC"]],"未チェック優先"],
]);

####################################################################################################



####################################################################################################
# 問い合わせ
####################################################################################################

# 問い合わせタイプ
define("CONTACT_TYPE",[
    1 => "ご意見やご要望",
    2 => "不具合の報告",
    // 3 => "",
    100 => "その他",
]);
# 問い合わせ制限
define("MIN_CONTACT_DETAIL",10);
define("MAX_CONTACT_DETAIL",10000);

####################################################################################################



