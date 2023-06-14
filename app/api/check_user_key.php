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

    $user_auth = new UserAuth();

    # 受信
    $json_string = file_get_contents('php://input');
    $data = json_decode($json_string,true);

    $result_json = [];

    if(isset($data['user_id']) && $data['user_id']!==""){
        if(!$db->getUserDetailDataByKey($data['user_id'])){
            $result_json['result'] = true;
            $result_json['user_id'] = $data['user_id'];
            $result_json['message'] = '"'.$data['user_id'].'"'.'は使われていません。';
        }else{
            $result_json['result'] = false;
            $result_json['user_id'] = $data['user_id'];
            $result_json['message'] = '"'.$data['user_id'].'"'.'はすでに使われています。';
        }
    }else{
        $result_json['result'] = false;
        $result_json['user_id'] = $data['user_id'];
        $result_json['message'] = '不正なアクセスです。';
    }

    /* jsonデータを返して終了 */
    $json_string = json_encode($result_json);
    header("Content-Type: application/json; charset=utf-8");
    echo $json_string;
    exit(0);
