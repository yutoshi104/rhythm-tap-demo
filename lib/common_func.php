<?php


####################################################################################################
# ↓↓↓ エラーページ ↓↓↓
####################################################################################################

# エラーページ出力(ユーザーページ)
function displayUserErrorExit($status_code, $title, $message) {
	# status code
    http_response_code($status_code);

    # global
    if(isset($GLOBALS['user_auth'])){
        $user_auth = $GLOBALS['user_auth'];
    }else{
        $user_auth = new UserAuth();
    }
    if(isset($GLOBALS['user_icon_url'])){
        $user_icon_url = $GLOBALS['user_icon_url'];
    }else{
        $user_icon_url = null;
    }
    if(isset($GLOBALS['user_icon_url_thumb'])){
        $user_icon_url_thumb = $GLOBALS['user_icon_url_thumb'];
    }else{
        $user_icon_url_thumb = null;
    }
    if(isset($GLOBALS['login_flg'])){
        $login_flg = $GLOBALS['login_flg'];
    }else{
        $login_flg = false;
    }

    # View
    $page = [
        "title"=>"エラー",
        "current"=>0,
        "login_flg"=>$login_flg,
        "user_data"=>$user_auth->user_data()!==null ? $user_auth->user_data() : null,
        "user_detail_data"=>$user_auth->user_detail_data()!==null ? $user_auth->user_detail_data() : null,
        "user_icon_url"=>$user_icon_url,
        "user_icon_url_thumb"=>$user_icon_url_thumb,
        "head_css"=>[],
        "head_js"=>[],
        "foot_js"=>[]
    ];
    if(file_exists(VIEW_DIR_USER."/header.inc")){
        include VIEW_DIR_USER."/header.inc";
    }
    if(file_exists(VIEW_DIR."/error.inc")){
        include VIEW_DIR."/error.inc";
    }
    if(file_exists(VIEW_DIR_USER."/footer.inc")){
        include VIEW_DIR_USER."/footer.inc";
    }
	exit();
}

# エラーページ出力(管理ページ)
function displayAdminErrorExit($status_code, $title, $message, $login_flg=false) {
	# status code
    http_response_code($status_code);

    # global
    global $admin_access;

    # View
    $page = [
        "title"=>"エラー",
        "current_row"=>0,
        "login_flg"=>$login_flg,
        "head_css"=>[],
        "head_js"=>[],
        "foot_js"=>[]
    ];
    if(file_exists(VIEW_DIR_ADMIN."/header.inc")){
        include VIEW_DIR_ADMIN."/header.inc";
    }
    if(file_exists(VIEW_DIR_ADMIN."/error.inc")){
        include VIEW_DIR_ADMIN."/error.inc";
    }
    if(file_exists(VIEW_DIR_ADMIN."/footer_empty.inc")){
        include VIEW_DIR_ADMIN."/footer_empty.inc";
    }
	exit();
}

####################################################################################################



####################################################################################################
# ↓↓↓ エラー通知 ↓↓↓
####################################################################################################

# エラーログ登録
function insertErrorLog($db, $error_code,$error_message,$error_detail,$url=null) {
    sendErrorLog($error_code,$error_message,$error_detail,$url);
}
# エラーログメール送信
function sendErrorLog($error_code,$error_message,$error_detail,$url=null) {
    if(defined('NO_SEND_MAIL') && NO_SEND_MAIL===true){
        return true;
    }
    # メール送信
    $site_name = SITE_NAME;
    $from = REPORT_FROM_EMAIL;
    $to = REPORT_TO_EMAIL;
    $subject = "{$site_name} エラー発生";
    $fromName = "{$site_name}";
    $time = getTime();
    $copyright = COPYRIGHT_EMAIL;
    if(is_null($url)){
        $url = HTTP_USER2.$_SERVER['REQUEST_URI'];
    }
    $body = <<< EOS

{$site_name}でエラーが発生しています。

time: {$time}
URL: {$url}
error code: {$error_code}
error message: {$error_message}
error detail: {$error_detail}

※ 本メールは{$site_name}より自動でお送りしています。
※ 送信専用のアドレスから送信しているため、送信元のアドレス宛にご返信いただいても受信できません。予めご了承ください。
※ お問い合わせは、{$site_name}の問い合わせフォームよりお願いいたします。

{$copyright}

EOS;

    send_mail($to, $subject, $body, $from, $fromName);
    return true;
}

####################################################################################################



####################################################################################################
# ↓↓↓ セキュリティ操作 ↓↓↓
####################################################################################################

# XSS対策：エスケープ処理   phpで出力するときに使用
function h($str){
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
# URLエスケープ
function u($str) {
	return urlencode($str);
}
# セッションハイジャック対策：セッションIDの振り直し
function change_session_id(){
    session_regenerate_id(true);
}
# CSRF対策
function setToken(){
    $csrf_token = bin2hex(openssl_random_pseudo_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
    return $csrf_token;
}
# メールヘッダーインジェクション対策
function get_url_password(){
    $pass = hash('sha256', openssl_random_pseudo_bytes(32));
    return $pass;
}
# br
function br($str) {
	return str_replace("\n", "<br>", $str);
}

# シード値生成
function make_seed(){
    list($usec, $sec) = explode(' ', microtime());
    return (float) $sec + ((float) $usec * 100000);
}
# ランダムキー生成(英数字)
function makeRandomKey($len = 8){
    $passwd = "";
    for ($i = 0; $i < $len; $i++){
        srand(make_seed());
        $str = base_convert((string) rand(0, 35), 10, 36);
        srand(make_seed());
        $passwd .= (rand(0, 1) ? strtoupper($str) : $str);
    }
    return $passwd;
}
# ランダムID生成(16進数)
function makeRandomId($length=64){
    $bytes = $length/2;
    return bin2hex(openssl_random_pseudo_bytes($bytes));
}

# パスワード暗号化
function encryptPassword($password){
    return password_hash($password, PASSWORD_BCRYPT, ["cost" => 8]);
}

# パスワード確認
function verifyPassword($password, $encrypted){
    return password_verify($password, $encrypted);
}

# 文字列虫食い
function strWormEaten($str,$safe=2,$safe_start=0){
    $len = strlen($str);
    $worm_eaten_front = str_repeat("*", $safe_start);
    $worm_eaten_back = str_repeat("*", $len-$safe-$safe_start);
    return $worm_eaten_front.substr($str,$safe_start,$safe).$worm_eaten_back;
}

####################################################################################################



####################################################################################################
# ↓↓↓ 汎用 ↓↓↓
####################################################################################################

# 時間取得
function getTime(){
    return date("Y-m-d H:i:s");
}
# メール送信
function send_mail($to, $title, $body, $fromAddress = "", $fromName = "", $additional_headers = array()) {
    if(defined('NO_SEND_MAIL') && NO_SEND_MAIL===true){
        return true;
    }
    mb_language("ja");
    mb_internal_encoding("UTF-8");

    $tmp = NULL;
    $envelope_from = "";
    if ($fromAddress !== "") {
            if ($fromName !== "") {
                    $tmp = sprintf("From: %s <%s>\n", mb_encode_mimeheader($fromName), $fromAddress);
            } else {
                    $tmp = sprintf("From: %s\n", $fromAddress);
            }
            $envelope_from = "-f {$fromAddress}";
    }
    foreach ($additional_headers as $k => $v) {
            if ($tmp === NULL) {
                    $tmp = sprintf("%s: %s\n", $k, $v);
            } else {
                    $tmp .= sprintf("%s: %s\n", $k, $v);
            }
    }

    if ($envelope_from) {
            return mb_send_mail($to, $title, $body, $tmp, $envelope_from);
    } else {
            return mb_send_mail($to, $title, $body, $tmp);
    }
}

# ページネーション作成
function getPagerHtml($current, $limit, $total_count, $link, $param_name="p", $url_params=array()){
    $pager_conf = array(
        "prev" => 1,
        "next" => 1
    );
    if( $total_count == 0 ){
        return "";
    }
    $opt = "&".http_build_query($url_params);

    $html = "";
    $html .= '<nav class="my-2">'."\n";
    $html .= '<ul class="pagination justify-content-center m-0">'."\n";
    if( $current > 1 ){
        $html .= sprintf('<li class="page-item"><a class="page-link" href="%s?%s=%d%s">&lt;&lt;</a></li>'."\n", $link, $param_name, $current-1, $opt);
    }
    $max = ceil(($total_count / $limit));
    $start = ( ($current - $pager_conf["prev"]) <= 0 ) ? 1 : $current - $pager_conf["prev"];
    $end = ( ($current + $pager_conf["next"]) > $max ) ? $max : $current + $pager_conf["next"];
    if( $start > 1 ){
        $html .= sprintf('<li class="page-item"><a class="page-link" href="%s?%s=1%s">1</a></li>'."\n", $link, $param_name, $opt);
        $html .= "...";
    }
    for( $i = $start;$i <= $end;$i++ ){
        if($i == $current){
            $class = " active";
            $html .= sprintf('<li class="page-item%s"><a class="page-link" href="%s?%s=%d%s">%d</a></li>'."\n", $class, $link, $param_name, $i, $opt, $i);
        }else{
            $html .= sprintf('<li class="page-item"><a class="page-link" href="%s?%s=%d%s">%d</a></li>'."\n", $link, $param_name, $i, $opt, $i);
        }
    }
    if( $end < $max ){
        $html .= "...";
        $html .= sprintf('<li class="page-item"><a class="page-link" href="%s?%s=%d%s">%d</a></li>'."\n", $link, $param_name, $max, $opt, $max);
    }
    if($current < $max){
        $html .= sprintf('<li class="page-item"><a class="page-link" href="%s?%s=%d%s">&gt;&gt;</a></li>'."\n", $link, $param_name, $current+1, $opt);
    }
    $html .= "</ul>\n";
    $html .= "</nav>\n";
    return $html;
}

# MySQLでの検索用エスケープ
function mysqlEscapeForLike($value){
    $value = str_replace("\\","\\\\",$value);
    $value = str_replace("%","\%",$value);
    $value = str_replace("_","\_",$value);
    return $value;
}

# フォルダ内からランダム選択
function selectFileRandom($folder_path,$extension="*",$num=1){
    $arrayPath = glob($folder_path."/".$extension);
    if(count($arrayPath) > 0){
        srand();
        $filepath = $arrayPath[(int)array_rand($arrayPath,$num)];
        return $filepath;
    }else{
        return false;
    }
}

# 時間表示
/**
 * mysqlのdatetime('2021/01/01 00:00:00')をフォーマット表示
 * $method=1: Y (int)
 * $method=2: m (int)
 * $method=3: d (int)
 * $method=4: H (int)
 * $method=5: i (int)
 * $method=6: s (int)
 * 
 * $method=10: Y/m/d H:i:s (str)
 * $method=11: Y/m/d H:i (str)
 * $method=12: Y/m/d H (str)
 * $method=13: Y/m/d (str)
 * $method=14: Y/m (str)
 * $method=15: m/d (str)
 * 
 * $method=20: Y年m月d日 H時i分s秒 (str)
 * $method=21: Y年m月d日 H時i分 (str)
 * $method=22: Y年m月d日 H時 (str)
 * $method=23: Y年m月d日 (str)
 * $method=24: Y年m月 (str)
 * $method=25: m月d日 (str)
 * 
 */
function displayTime($datetime, $method=10){
    switch ($method) {
        case 1:
            return (int)date('Y',  strtotime($datetime));
        case 2:
            return (int)date('m',  strtotime($datetime));
        case 3:
            return (int)date('d',  strtotime($datetime));
        case 4:
            return (int)date('H',  strtotime($datetime));
        case 5:
            return (int)date('i',  strtotime($datetime));
        case 6:
            return (int)date('s',  strtotime($datetime));
        case 10:
            return date('Y/m/d H:i:s',  strtotime($datetime));
        case 11:
            return date('Y/m/d H:i',  strtotime($datetime));
        case 12:
            return date('Y/m/d H',  strtotime($datetime));
        case 13:
            return date('Y/m/d',  strtotime($datetime));
        case 14:
            return date('Y/m',  strtotime($datetime));
        case 15:
            return date('m/d',  strtotime($datetime));
        case 20:
            return date('Y年m月d日 H時i分s秒',  strtotime($datetime));
        case 21:
            return date('Y年m月d日 H時i分',  strtotime($datetime));
        case 22:
            return date('Y年m月d日 H時',  strtotime($datetime));
        case 23:
            return date('Y年m月d日',  strtotime($datetime));
        case 24:
            return date('Y年m月',  strtotime($datetime));
        case 25:
            return date('m月d日',  strtotime($datetime));
        case 30:
            return date('Y-m-d H:i:s',  strtotime($datetime));
        case 31:
            return date('Y-m-d H:i',  strtotime($datetime));
        case 32:
            return date('Y-m-d H',  strtotime($datetime));
        case 33:
            return date('Y-m-d',  strtotime($datetime));
        case 34:
            return date('Y-m',  strtotime($datetime));
        case 35:
            return date('m-d',  strtotime($datetime));
    }
}

# 時間の差分を表示
function displayTimeDiff($datetime1,$datetime2=null,$method=1){
    if($datetime2===null){
        $datetime2 = getTime();
    }
    $dt1 = new DateTime($datetime1);
    $dt2 = new DateTime($datetime2);
    $diff = $dt1->diff($dt2);
    $ba = $diff->format('%R')==="+" ? "前" : "後";
    $y = (int)$diff->format('%y');
    $m = (int)$diff->format('%m');
    $d = (int)$diff->format('%d');
    $h = (int)$diff->format('%h');
    $i = (int)$diff->format('%i');
    $s = (int)$diff->format('%s');

    
    $return_diff = "";
    switch ($method) {
        ### 最初の0は非表示・途中の0は表示 ###
        case 1: // %d年%02dヶ月%02d日%02d時間%02d分%02d秒
        case 2: // %d年%02dヶ月%02d日%02d時間%02d分%
        case 3: // %d年%02dヶ月%02d日%02d時間
        case 4: // %d年%02dヶ月%02d日
        case 5: // %d年%02dヶ月
        case 6: // %d年%dヶ月%d日%d時間%d分%d秒
        case 7: // %d年%dヶ月%d日%d時間%d分
        case 8: // %d年%dヶ月%d日%d時間
        case 9: // %d年%dヶ月%d日
        case 10: // %d年%dヶ月
        case 11: // %d年
            if($y!==0 || $return_diff!==""){
                $return_diff .= $y."年";
            }
            if(in_array($method,[1,2,3,4,5,6,7,8,9,10])){
                if($m!==0 || $return_diff!==""){
                    if(in_array($method,[1,2,3,4,5])){
                        $return_diff .= sprintf("%02dヶ月", $m);
                    }else{
                        $return_diff .= sprintf("%dヶ月", $m);
                    }
                }
            }
            if(in_array($method,[1,2,3,4,6,7,8,9])){
                if($d!==0 || $return_diff!==""){
                    if(in_array($method,[1,2,3,4])){
                        $return_diff .= sprintf("%02d日", $d);
                    }else{
                        $return_diff .= sprintf("%d日", $d);
                    }
                }
            }
            if(in_array($method,[1,2,3,6,7,8])){
                if($h!==0 || $return_diff!==""){
                    if(in_array($method,[1,2,3])){
                        $return_diff .= sprintf("%02d時間", $h);
                    }else{
                        $return_diff .= sprintf("%d時間", $h);
                    }
                }
            }
            if(in_array($method,[1,2,6,7])){
                if($i!==0 || $return_diff!==""){
                    if(in_array($method,[1,2])){
                        $return_diff .= sprintf("%02d分", $i);
                    }else{
                        $return_diff .= sprintf("%d分", $i);
                    }
                }
            }
            if(in_array($method,[1,6])){
                if($s!==0 || $return_diff!==""){
                    if(in_array($method,[1])){
                        $return_diff .= sprintf("%02d秒", $s);
                    }else{
                        $return_diff .= sprintf("%d秒", $s);
                    }
                }
            }
            if($return_diff===""){
                $return_diff = "現在";
            }else{
                $return_diff .= $ba;
            }
            break;
        ### 最初の0も表示・途中の0は表示 ###
        case 31: // %d年%02dヶ月%02d日%02d時間%02d分%02d秒
        case 32: // %d年%02dヶ月%02d日%02d時間%02d分%
        case 33: // %d年%02dヶ月%02d日%02d時間
        case 34: // %d年%02dヶ月%02d日
        case 35: // %d年%02dヶ月
        case 36: // %d年%dヶ月%d日%d時間%d分%d秒
        case 37: // %d年%dヶ月%d日%d時間%d分
        case 38: // %d年%dヶ月%d日%d時間
        case 39: // %d年%dヶ月%d日
        case 40: // %d年%dヶ月
        case 41: // %d年
            $return_diff .= $y."年";
            if(in_array($method,[31,32,33,34,35,36,37,38,39,40])){
                if($return_diff!==""){
                    $return_diff .= $m."ヶ月";
                }
            }
            if(in_array($method,[31,32,33,34,36,37,38,39])){
                if($return_diff!==""){
                    $return_diff .= $d."日";
                }
            }
            if(in_array($method,[31,32,33,36,37,38])){
                if($return_diff!==""){
                    $return_diff .= sprintf("%02d時間", $h);
                }
            }
            if(in_array($method,[31,32,36,37])){
                if($return_diff!==""){
                    $return_diff .= sprintf("%02d分", $i);
                }
            }
            if(in_array($method,[31,36])){
                if($return_diff!==""){
                    $return_diff .= sprintf("%02d秒", $s);
                }
            }
            if($return_diff===""){
                $return_diff = "現在";
            }else{
                $return_diff .= $ba;
            }
            break;
        ### 最初のみ表示 ###
        case 100:
            if($y!==0 && $return_diff===""){
                $return_diff .= $y."年";
            }
            if($m!==0 && $return_diff===""){
                $return_diff .= $m."ヶ月";
            }
            if($d!==0 && $return_diff===""){
                $return_diff .= $d."日";
            }
            if($h!==0 && $return_diff===""){
                $return_diff .= sprintf("%d時間", $h);
            }
            if($i!==0 && $return_diff===""){
                $return_diff .= sprintf("%d分", $i);
            }
            if($s!==0 && $return_diff===""){
                $return_diff .= sprintf("%d秒", $s);
            }
            if($return_diff===""){
                $return_diff = "現在";
            }else{
                $return_diff .= $ba;
            }
            break;
        }
    return $return_diff;
}

# 時間の差分を表示(2)
function displayTimeDiffPast($datetime1,$datetime2=null){
    if($datetime2===null){
        $datetime2 = getTime();
    }
    $dt1 = new DateTime($datetime1);
    $dt2 = new DateTime($datetime2);
    $diff = $dt1->diff($dt2);
    $ba = $diff->format('%R')==="+" ? "前" : "後";
    $y = (int)$diff->format('%y');
    $m = (int)$diff->format('%m');
    $d = (int)$diff->format('%d');
    $h = (int)$diff->format('%h');
    $i = (int)$diff->format('%i');
    $s = (int)$diff->format('%s');

    $return_diff = "";
    if($y!==0 || $return_diff!==""){
        $return_diff .= $y."年";
    }
    if($m!==0 || $return_diff!==""){
        $return_diff .= $m."ヶ月";
    }
    if($d!==0 || $return_diff!==""){
        $return_diff .= $d."日";
    }
    if($h!==0 || $return_diff!==""){
        $return_diff .= sprintf("%02d時間", $h);
    }
    if($i!==0 || $return_diff!==""){
        $return_diff .= sprintf("%02d分", $i);
    }
    if($s!==0 || $return_diff!==""){
        $return_diff .= sprintf("%02d秒", $s);
    }
    if($return_diff===""){
        $return_diff = "現在";
    }else{
        $return_diff .= $ba;
    }
    return $return_diff;
}

# 指定した文字数毎に文字列を挿入
function insertStr($text,$insert,$num){
    $returnText = $text;
    $text_len = mb_strlen($text, "utf-8");
    $insert_len = mb_strlen($insert, "utf-8");
    for($i=0; ($i+1)*$num<$text_len; $i++) {
        $current_num = $num+$i*($insert_len+$num);
        $returnText = preg_replace("/^.{0,$current_num}+\K/us", $insert, $returnText);
    }
    return $returnText;
}


####################################################################################################



####################################################################################################
# ↓↓↓ バリデーション ↓↓↓
####################################################################################################

# 値があるかどうか
function isValid($value){
    if($value!==null && $value!==""){
        return true;
    }else{
        return false;
    }
}
# 文字列の長さ (min以上max以下であるか)
function isValidStrLen($str, $min, $max) {
	if( mb_strlen($str) < $min || mb_strlen($str) > $max ) {
		return false;
	} else {
		return true;
	}
}
# 数値の範囲 (min以上max以下であるか)
function isValidNumRange($num, $min, $max) {
    if( !is_numeric($num) ){
        return false;
    }
	if( (double)$num < $min || (double)$num > $max ) {
		return false;
	} else {
		return true;
	}
}
# 適したユーザーIDかどうか
function isUserKey($key){
	if(preg_match('/^[a-zA-Z0-9_]+$/',$key)){
        return true;
    }else{
        return false;
    }
}
# メールアドレスかどうか
function isEmail($email){
    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        return true;
    }else{
        return false;
    }
}
# URLかどうか
function isUrl($url){
    if(filter_var($url,FILTER_VALIDATE_URL,FILTER_FLAG_PATH_REQUIRED)){
        return true;
    }else{
        return false;
    }
}
# パスワードとして適切か(少なくとも英数をそれぞれ1文字以上含んでいるか)
function isPassword($password){
    $pattern = '/^(?=.*?[a-zA-Z])(?=.*?[0-9])[a-zA-Z0-9!"#$%&\'()*,\-\.\/:;<>?@\[\]\^_`{|}~]*$/';
	if(preg_match($pattern, $password)){
        return true;
    }else{
        return false;
    }
}
# 正しい日付かどうか
function isDateTime($datetime){
    try {
        $obj = new DateTime($datetime);
        if(DateTime::getLastErrors()['warning_count'] > 0 || DateTime::getLastErrors()['error_count'] > 0){
            return false;
        }else{
            return true;
        }
    } catch (Exception $e) {
        return false;
    }
}
# NGワードチェック
# ngword: true
function check_ngword($word){
    $_word = preg_replace(OK_WORDS, "*", $word);
    preg_replace(NG_WORDS, "*", $_word, -1, $count);
    return $count;
}


// # メールアドレスバリデーション
// function emailValidation($email){
//     $err = [];
//     if(!$email){
//         $err[] = 'メールアドレスを記入してください。';
//     }
//     if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
//         $err[] = 'メールアドレスではありません。';
//     }
//     return $err;
// }
// # パスワードバリデーション
// function passwordValidation($password){
//     $err = [];
//     if(!$password){
//         $err[] = 'パスワードを記入してください。';
//     }
//     if(isValidStrLen($password,8,32)===false){
//         $err[] = 'パスワードは8文字以上32文字以下にしてください。';
//     }
//     $pattern = '/^(?=.*?[a-zA-Z])(?=.*?[0-9])[a-zA-Z0-9!"#$%&\'()*,\-\.\/:;<>?@\[\]\^_`{|}~]*$/';
// 	if( !preg_match($pattern, $password) ){
//         $err[] = 'パスワードには少なくとも英数をそれぞれ1文字以上含んでください。';
//     }
//     return $err;
// }
// # URLバリデーション
// function urlValidation($url){
//     $err = [];
//     if(!$url){
//         $err[] = 'URLを記入してください。';
//     }
//     if(!filter_var($url,FILTER_VALIDATE_URL,FILTER_FLAG_PATH_REQUIRED)){
//         $err[] = 'URLではありません。';
//     }
//     return $err;
// }


# recaptchaチェック
/**
 *      <div class="g-recaptcha" data-sitekey="<?php echo G_RECAPTCHA_SITE_KEY; ?>" data-callback="clearcall"></div>
 *      <script src="https://www.google.com/recaptcha/api.js" async defer></script>
 *      <script>
 *          function clearcall(code) {
 *              if(code !== ""){
 *                  document.getElementById('submit_button').disabled = false;
 *              }
 *          }
 *      </script>
 */
function checkRecaptcha($response=null){
    if(defined('G_RECAPTCHA_NOCHECK') && G_RECAPTCHA_NOCHECK===true){
        return true;
    }
    if($response===null){
        $response = $_POST["g-recaptcha-response"];
    }
    $recaptcha = htmlspecialchars($response,ENT_QUOTES,'UTF-8');
    if(isset($recaptcha)){
        $captcha = $recaptcha;
        $secretKey = G_RECAPTCHA_SECRET_KEY;
        $resp = @file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$captcha}");
        $resp_result = json_decode($resp,true);
        if(intval($resp_result["success"]) !== 1) {
            displayUserErrorExit(400,"400 Bad Request","不正なアクセスです。");
        }else{
            return true;
        }
    }else{
        return false;
    }
}



####################################################################################################



####################################################################################################
# ↓↓↓ CSV ↓↓↓
####################################################################################################

// CSVエスケープ
function csvEscape($value){
    return str_replace('"', '""', $value);
}
// CSVの１行作成
function makeArrToCsvRow($arr){
    $row = "";
    foreach($arr as $a){
        $row .= '"'.csvEscape($a).'",';
    }
    $row = rtrim($row,",");
    $row .= "\n";
    return $row;
}

####################################################################################################





####################################################################################################
# ↓↓↓ メールアドレススパム対策 ↓↓↓
####################################################################################################

function hideEmail($email) { 
    $character_set  = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
    $key = str_shuffle($character_set); $cipher_text = '';  $id = 'e'.rand(1,999999999);
    for ($i=0; $i<strlen($email); $i+=1) $cipher_text.= $key[strpos($character_set,$email[$i])];
        $script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";';
        $script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));';
        $script.= 'document.getElementById("'.$id.'").innerHTML=""+d+""';
        $script = "eval(\"".str_replace(array("\\",'"'),array("\\\\",'\"'), $script)."\")";
        $script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>';
    return '<span id="'.$id.'">[javascript protected email address]</span>'.$script;
}
function hideEmailMailTo($email) { 
    $character_set  = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
    $key = str_shuffle($character_set); $cipher_text = '';  $id = 'e'.rand(1,999999999);
    for ($i=0; $i<strlen($email); $i+=1) $cipher_text.= $key[strpos($character_set,$email[$i])];
        $script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";';
        $script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));';
        $script.= 'document.getElementById("'.$id.'").innerHTML="<a href=\\"mailto:"+d+"\\">"+d+"</a>"';
        $script = "eval(\"".str_replace(array("\\",'"'),array("\\\\",'\"'), $script)."\")";
        $script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>';
    return '<span id="'.$id.'">[javascript protected email address]</span>'.$script;
}

####################################################################################################
