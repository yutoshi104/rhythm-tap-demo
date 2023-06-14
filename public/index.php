<?php

if(empty($_SERVER['REQUEST_URI'])) {
    exit();
}



# トップページ
if(preg_match('/^\/?$/',$_SERVER['REQUEST_URI'])){
    include("../app/main/top.php");
    exit();
}

# デモページ
if(preg_match('/^\/acceleration\/?$/',$_SERVER['REQUEST_URI'])){
    include("../app/main/acceleration.php");
    exit();
}
if(preg_match('/^\/gyro\/?$/',$_SERVER['REQUEST_URI'])){
    include("../app/main/gyro.php");
    exit();
}
if(preg_match('/^\/magnetism\/?$/',$_SERVER['REQUEST_URI'])){
    include("../app/main/magnetism.php");
    exit();
}
if(preg_match('/^\/vibration\/?$/',$_SERVER['REQUEST_URI'])){
    include("../app/main/vibration.php");
    exit();
}
if(preg_match('/^\/audio_output\/?$/',$_SERVER['REQUEST_URI'])){
    include("../app/main/audio_output.php");
    exit();
}
if(preg_match('/^\/long_press_screen\/?$/',$_SERVER['REQUEST_URI'])){
    include("../app/main/long_press_screen.php");
    exit();
}



####################################################################################################
# 認証系
####################################################################################################

// # ログイン
// if(preg_match('/^\/login\/?(\?.+)?$/',$_SERVER['REQUEST_URI'])){
//     include("../app/user/auth/login.php");
//     exit();
// }
// # ログアウト
// if(preg_match('/^\/logout\/?$/',$_SERVER['REQUEST_URI'])){
//     include("../app/user/auth/logout.php");
//     exit();
// }
// # 新規登録
// if(preg_match('/^\/regist\/?$/',$_SERVER['REQUEST_URI'])){
//     include("../app/user/auth/email_auth.php");
//     exit();
// }
// # 新規登録
// if(preg_match('/^\/main_regist\/([0-9a-f]{64})\/?$/',$_SERVER['REQUEST_URI'],$match)){
//     session_start();
//     session_regenerate_id(true);
//     $_SESSION['psid'] = $match[1];
//     include("../app/user/auth/regist.php");
//     exit();
// }
// # パスワード忘れ
// if(preg_match('/^\/forgot\/?$/',$_SERVER['REQUEST_URI'])){
//     include("../app/user/auth/forgot.php");
//     exit();
// }
// # パスワードリセット
// if(preg_match('/^\/reset\/([0-9a-f]{64})\/?$/',$_SERVER['REQUEST_URI'],$match)){
//     session_start();
//     session_regenerate_id(true);
//     $_SESSION['psid'] = $match[1];
//     include("../app/user/auth/reset.php");
//     exit();
// }
// # 退会
// if(preg_match('/^\/leave\/?$/',$_SERVER['REQUEST_URI'])){
//     include("../app/user/auth/leave.php");
//     exit();
// }

// # マイページ(自分以外も表示するとき用)
// // if(preg_match('/^\/profile\/([a-zA-Z0-9_]+)\/?(\?.+)?$/',$_SERVER['REQUEST_URI'],$match)){
// //     session_start();
// //     session_regenerate_id(true);
// //     $_SESSION['key'] = $match[1];
// //     include("../app/user/mypage/mypage.php");
// //     exit();
// // }
// # マイページ(自分用)
// if(preg_match('/^\/mypage\/?(\?.+)?$/',$_SERVER['REQUEST_URI'],$match)){
//     session_start();
//     session_regenerate_id(true);
//     include("../app/user/mypage/mypage.php");
//     exit();
// }
// # プロフィール編集
// if(preg_match('/^\/mypage\/edit\/?$/',$_SERVER['REQUEST_URI'])){
//     include("../app/user/mypage/profile_edit.php");
//     exit();
// }

// # ユーザIDチェック
// if(preg_match('/^\/api\/check_user_id\/?$/',$_SERVER['REQUEST_URI'])){
//     include("../app/api/check_user_key.php");
//     exit();
// }
// # コンテンツ画像表示
// if(preg_match('/^\/api\/get_image_content\/?$/',$_SERVER['REQUEST_URI'])){
//     include("../app/api/get_img_content.php");
//     exit();
// }

####################################################################################################





####################################################################################################
# サポート系
####################################################################################################

// # 問い合わせ
// if(preg_match('/^\/contact\/?$/',$_SERVER['REQUEST_URI'])){
//     include("../app/supports/contact.php");
//     exit();
// }
// # 管理者について
// if(preg_match('/^\/about\/admin\/?$/',$_SERVER['REQUEST_URI'])){
//     include("../app/supports/about_admin.php");
//     exit();
// }
// # 利用規約
// if(preg_match('/^\/terms\/?$/',$_SERVER['REQUEST_URI'])){
//     include("../app/supports/terms.php");
//     exit();
// }

####################################################################################################





# エラーページ出力
$lib_dir = __DIR__."/../lib";
require_once($lib_dir."/common_inc.php");
displayUserErrorExit(404,"404 Not Found","ご指定のページは存在しません。");
exit();