<?php

require_once(__DIR__."/BiuDb.php");
require_once(__DIR__."/LogDb.php");

class Db {
    use BiuDb;
    use LogDb;

    protected $mysqli = NULL;
    // protected $cache = NULL;
    protected $err_msg = "";

    public function __construct() {
    }

    public function __destruct() {
        if ($this->mysqli !== NULL) {
            $this->mysqli->close();
        }
    }

    public function getInstance() {
        return $this->mysqli;
    }

    public function getErr() {
        return $this->err_msg;
    }


    ####################################################################################################
    # インスタンス初期化
    ####################################################################################################

	public function connect($host, $user, $password, $db_name, $port = 3306, $charset = "utf8mb4", $timeout = 5) {
		$mysqli = mysqli_init();
		if( !$mysqli ) {
			$this->err_msg = "mysqli init error";
			return false;
		}
		if( !$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, $timeout) ) {
			$this->err_msg = "mysqli set timeout error";
			return false;
		}
		if( !$mysqli->real_connect($host, $user, $password, $db_name, $port) ) {
			$this->err_msg = "mysqli connect error";
			return false;
		}
		if( !$mysqli->set_charset($charset) ) {
			$this->err_msg = "mysqli set charset error";
			return false;
		}
		if( !$mysqli->autocommit(true) ) {
			$this->err_msg = "mysqli set autocommit error";
			return false;
		}

		$this->mysqli = $mysqli;
		return true;
	}

	public function init($host, $user, $password, $db_name, $port = 3306, $charset = "utf8mb4", $timeout = 5) {
		// $cache = new Cache();
		// if( $cache->init(REDIS_INFO["host"], REDIS_INFO["port"], REDIS_PREFIX) === false ) {
		// 	$this->err_msg = "redis init error";
		// 	return false;
		// }
		// $this->cache = $cache;
		return $this->connect($host, $user, $password, $db_name, $port, $charset, $timeout);
	}

	public function init_master() {
        return $this->init(DB_HOST_MASTER, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT_MASTER, DB_CHARSET, DB_TIMEOUT);
    }

	public function init_slave() {
        return $this->init(DB_HOST_SLAVE, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT_SLAVE, DB_CHARSET, DB_TIMEOUT);
    }

    ####################################################################################################



    ####################################################################################################
    # トランザクション処理
    ####################################################################################################

    public function begin() {
        if ($this->mysqli->begin_transaction() === false) {
            $this->err_msg = "mysqli::begin_transaction: error";
            return false;
        }
        return true;
    }

    public function rollback() {
        if ($this->mysqli->rollback() === false) {
            $this->err_msg = "mysqli::rollback: error";
            return false;
        }
        return true;
    }

    public function commit() {
        if ($this->mysqli->commit() === false) {
            $this->err_msg = "mysqli::commit: error";
            return false;
        }
        return true;
    }

    ####################################################################################################



    ####################################################################################################
    # 操作
    ####################################################################################################

    # 実行
	public function query($sql) {
        // $log_sql  = "insert into `sql_log`";
		// $log_sql .= sprintf(" set `ip`='%s' and `ua`='%s' and `sql_sentence`='%s' and `url`='%s' and `status`='%s' and `intime`='%s'", $this->escape($_SERVER['REMOTE_ADDR']), $this->escape($_SERVER['HTTP_USER_AGENT']), $this->escape($sql), $this->escape($_SERVER['REQUEST_URI']), $this->escape(0), $this->escape(getTime()));
        // error_log($log_sql);
        // var_dump($this->mysqli->query($log_sql));
        return $this->mysqli->query($sql);
	}

    # MySQL エスケープ
    public function escape($str) {
        return $this->mysqli->real_escape_string($str);
    }

    # where配列をwhere文に
    /**
     * @param array $array      2重配列. "条件の配列"の連想配列. "条件の配列"の形式は,["カラム名","条件","条件値"(,"条件値2"...)].
     *  -> [["id","=","3"],
     *      ["id",">=","10"],
     *      ["intime",">","2021-03-10 00:00:00"],
     *      ["session_id","=",true,"0230298302938e80239"],  (->trueにより強制でクォーテーションで囲む)
     *      ["tel","LIKE","080-%"],
     *      ["address","LIKE","%練馬区%マンション%"],
     *      ["tel","REGEXP","^090-([0-9]|-)+"]]
     * @return string $where    where文.
     */
    public function array2where($array){
        $where = "";
        foreach($array as $arr){
            if($arr===[] || count($arr)<3){
                continue;
            }
            if($where===""){
                $where .= " WHERE";
            }else{
                $where .= " AND";
            }
            $i = 0;
            $quote_flg = false;
            foreach($arr as $a){
                if($i===0){
                    $where .= " `".$a."`";
                }else if($i===1){
                    $where .= " ".$a;
                    if( (strcasecmp($a,"LIKE")===0) || (strcasecmp($a,"REGEXP")===0) ){
                        $quote_flg = true;
                    }
                }else if($i>=2){
                    if($a===true){
                        $quote_flg = true;
                        continue;
                    }
                    if(is_numeric($a) && !$quote_flg){
                        $where .= " ".(float)$a;
                    }else{
                        $where .= " '".$this->escape($a)."'";
                    }
                }
                $i++;
            }
        }
        return $where;
    }

    public function array2where_past($array){
        $where = "";
        $flg = false;
        foreach($array as $arr){
            if($arr===[] || count($arr)<3){
                continue;
            }
            if(!$flg){
                $where .= " WHERE";
                $flg = true;
            }else{
                $where .= " AND";
            }
            $i = 0;
            $quote_flg = false;
            foreach($arr as $a){
                if( $i===1 && (strcasecmp($a,"LIKE")===0 || strcasecmp($a,"REGEXP")===0) ){
                    $quote_flg = true;
                }
                if($i===2){
                    $where .= " ";
                    if($quote_flg){
                        $where .= "'";
                    }
                    if($a==="'"){
                        $where .= $a;
                    }else{
                        $where .= $this->escape($a);
                    }
                }else if($i>2){
                    if($a==="'"){
                        $where .= $a;
                    }else{
                        $where .= $this->escape($a);
                    }
                }else{
                    $where .= " ".$a;
                }
                $i++;
            }
            if($quote_flg){
                $where .= "'";
            }
        }
        return $where;
    }

    # order配列をorder文に
    /**
     * @param array $array      ソートの配列. ソートの配列の形式は,["カラム名","昇順(ASC)or降順(DESC)"].
     *  -> ["id","ASC"]      //昇順
     *  -> ["intime","DESC"] //降順
     *  -> [["intime","DESC"],["id","ASC"]] //idを昇順にした後にintimeを降順 (追加)
     * @return string $order    order文.
     */
    public function array2order($array){
        $order = "";
        if(is_array($array) && count($array)===2){
            if(is_array($array[0])){
                foreach($array as $a){
                    if($order===""){
                        $order .= sprintf(" ORDER BY `%s` %s", $this->escape($a[0]), $this->escape($a[1]));
                    }else{
                        $order .= sprintf(", `%s` %s", $this->escape($a[0]), $this->escape($a[1]));
                    }
                }
            }else{
                $order .= sprintf(" ORDER BY `%s` %s", $this->escape($array[0]), $this->escape($array[1]));
            }
        }
        return $order;
    }

    # limit配列をlimit文に
    /**
     * @param array $array      上限の連想配列. 上限の配列の形式は,[("offset"=>何番目からのデータを取得するか,)"row_count"=>取得するデータの最大行数].
     *  -> ["offset"=>0,"row_count"=>100]
     *  -> ["row_count"=>30]
     * @return string $limit    limit文.
     */
    public function array2limit($array){
        $limit = "";
        if(is_array($array) && array_key_exists("row_count",$array) && is_numeric($array['row_count'])){
            $limit .= " LIMIT ";
            if(array_key_exists("offset",$array) && is_numeric($array['offset'])){
                $limit .= $this->escape($array["offset"]).",";
            }
            $limit .= $this->escape($array["row_count"]);
        }
        return $limit;
    }

    # data配列をdata文に
    /**
     * @param array $array      データの連想配列. データの配列の形式は,["column1"=>data1,"column2"=>data2,...].
     *  -> ["offset"=>0,"row_count"=>100]
     *  -> ["row_count"=>30]
     * @return string $limit    limit文.
     */
    public function array2data($array){
        $data = "";
        foreach($array as $key=>$value){
            if(is_numeric($value) && !is_string($value)){
                $data .= sprintf("`%s`=%s, ", $this->escape($key), $this->escape($value));
            }else if(is_null($value)){
                $data .= sprintf("`%s`= NULL, ", $this->escape($key));
            }else{
                $data .= sprintf("`%s`='%s', ", $this->escape($key), $this->escape($value));
            }
        }
        return mb_substr($data, 0, -2);
    }


    # 取得
    public function fetch($table_name, $columns="*", $where_array=[], $order_array=[], $limit_array=[]){
        if(is_array($columns)){
            $columns = implode(",",$columns);
        }
        $sql = sprintf("SELECT %s FROM `%s`", $columns, $this->escape($table_name));

        $sql .= $this->array2where($where_array);
        $sql .= $this->array2order($order_array);
        $sql .= $this->array2limit($limit_array);
// print_r($sql."<br>");
        $res = $this->query($sql);
        if($res && $res->num_rows > 0){
            return $res;
        }else{
            return false;
        }
    }

    # データ数取得
    public function getRowsNum($table_name, $where_array=[], $order_array=[], $limit_array=[]){
        $sql  = sprintf("SELECT found_rows() as total FROM `%s`", $this->escape($table_name));

        $sql .= $this->array2where($where_array);
        $sql .= $this->array2order($order_array);
        $sql .= $this->array2limit($limit_array);

        return $this->query($sql)->fetch_assoc()['total'];
    }

    # 挿入
    public function insert($table_name, $datas, $intime=false, $uptime=false){
        if($intime){
            $datas["intime"] = getTime();
        }
        if($uptime){
            $datas["uptime"] = getTime();
        }
        $sql  = sprintf("INSERT INTO `%s` SET ", $this->escape($table_name));
        $sql .= $this->array2data($datas);
        $res = $this->query($sql);
        if($res){
            return $this->mysqli->insert_id===0 ? true : $this->mysqli->insert_id;
        }else{
            return false;
        }
    }

    # 更新
    public function update($table_name, $datas, $uptime=false, $where_array=[], $order_array=[], $limit_array=[]){
        if($uptime){
            $datas["uptime"] = getTime();
        }
        $sql  = sprintf("UPDATE `%s` SET ", $this->escape($table_name));
        $sql .= $this->array2data($datas);
        $sql .= $this->array2where($where_array);
        $sql .= $this->array2order($order_array);
        $sql .= $this->array2limit($limit_array);
        return $this->query($sql);
    }

    # 置換(既存のものがある場合は更新、ない場合は挿入)  : 非推奨(intimeが更新されてしまうから)
    public function replace($table_name, $datas, $intime=false, $uptime=false){
        if($intime){
            $datas["intime"] = getTime();
        }
        if($uptime){
            $datas["uptime"] = getTime();
        }
        $sql  = sprintf("REPLACE INTO `%s` SET ", $this->escape($table_name));
        $sql .= $this->array2data($datas);
        $res = $this->query($sql);
        if($res){
            return $this->mysqli->insert_id===0 ? true : $this->mysqli->insert_id;
        }else{
            return false;
        }
    }

    # 論理削除
    public function logicalDelete($table_name, $uptime=false, $where_array=[], $order_array=[], $limit_array=[]){
        return $this->update($table_name, ["delf"=>1], $uptime, $where_array, $order_array, $limit_array);
    }
    
    # 物理削除
    public function physicalDelete($table_name, $where_array=[["1","=","0"]], $order_array=[], $limit_array=[]){
        $sql  = sprintf("DELETE FROM `%s`", $this->escape($table_name));
        $sql .= $this->array2where($where_array);
        $sql .= $this->array2order($order_array);
        $sql .= $this->array2limit($limit_array);
        return $this->query($sql);
    }

    # 論理復元
    public function logicalRestore($table_name, $uptime=false, $where_array=[], $order_array=[], $limit_array=[]){
        return $this->update($table_name, ["delf"=>0], $uptime, $where_array, $order_array, $limit_array);
    }

    # 最後に挿入したレコードのIDを取得
    public function getLastInsertId() {
		$sql  = "select last_insert_id() as last_id";
		$rs = $this->query($sql);
		$f = $rs->fetch_assoc();
		return $f["last_id"];
	}

    # 特定の条件のデータが存在するか
    public function isExistData($table_name, $where_array=[]) {
		$sql = sprintf("SELECT COUNT(*) AS count FROM `%s`", $this->escape($table_name));
		$sql .= $this->array2where($where_array);
		$res = $this->query($sql);
		$res = $res->fetch_assoc();
		if( $res["count"] > 0 ) {
			return true;
		}else{
			return false;
		}
	}

    ####################################################################################################


}
