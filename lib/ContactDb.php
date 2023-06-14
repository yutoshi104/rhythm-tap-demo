<?php

trait ContactDb {


	####################################################################################################
    # contactテーブル
    ####################################################################################################

	# 問い合わせ全取得
	public function getContact(){
		$res = $this->fetch("contact", "*", null, ["intime","DESC"]);
		if($res){
			return $res->fetch_all(MYSQLI_ASSOC);
		}else{
			return null;
		}
	}
	# 問い合わせ取得(contact_id)
	public function getContactByContactId($contact_id){
		$res = $this->fetch("contact", "*", [["contact_id","=",$contact_id]], ["intime","DESC"]);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC);
		}else{
			return null;
		}
	}
	# 問い合わせ取得(For Search)
	public function getContactDataSearch($name="",$email="",$type=null,$q="all",$sort_num=0,$from=0,$num=ADMIN_PAGING_CHECK){
		$where = [];
		if($name){
			$where[] = ["contact_name","LIKE","%".$name."%"];
		}
		if($name){
			$where[] = ["contact_email","LIKE","%".$email."%"];
		}
		if($type){
			$where[] = ["contact_type","=",$type];
		}
		if($q==="nochecked"){
			$where[] = ["checked","=",0];
		}else if($q==="checked"){
			$where[] = ["checked","=",1];
		}
		$res = $this->fetch("contact", "*", $where, ADMIN_SORT_PATERN[$sort_num][0], ["offset"=>$from,"row_count"=>$num]);
		if($res){
			return $res->fetch_all(MYSQLI_ASSOC);
		}else{
			return null;
		}
	}
	# 問い合わせ件数取得(For Search)
	public function getContactDataNumSearch($name="",$email="",$type=null,$q="all"){
		$where = [];
		if($name){
			$where[] = ["contact_name","LIKE","%".$name."%"];
		}
		if($name){
			$where[] = ["contact_email","LIKE","%".$email."%"];
		}
		if($type){
			$where[] = ["contact_type","=",$type];
		}
		if($q==="nochecked"){
			$where[] = ["checked","=",0];
		}else if($q==="checked"){
			$where[] = ["checked","=",1];
		}
		$res = $this->fetch("contact", "COUNT(*) as num", $where);
		if($res){
			return $res->fetch_array(MYSQLI_ASSOC)['num'];
		}else{
			return null;
		}
	}
	# 問い合わせ登録
    public function insertContact($user_id,$contact_name, $contact_email, $contact_type, $contact_detail){
		$insert = [];
		$insert['user_id'] = $user_id;
		$insert['contact_name'] = $contact_name;
		$insert['contact_email'] = $contact_email;
		$insert['contact_type'] = $contact_type;
		$insert['contact_detail'] = $contact_detail;
        return $this->insert("contact", $insert, true);
    }
	# 問い合わせチェック済み
	public function updateContactChecked($contact_id){
		return $this->update("contact", ["checked"=>1], false, [["contact_id","=",$contact_id]]);
	}
	# 問い合わせチェック解除
	public function updateContactNoChecked($contact_id){
		return $this->update("contact", ["checked"=>0], false, [["contact_id","=",$contact_id]]);
	}
	# 問い合わせステータスオン
	public function updateContactStatusOn($contact_id){
		return $this->update("contact", ["status"=>1], false, [["contact_id","=",$contact_id]]);
	}
	# 問い合わせステータスオフ
	public function updateContactStatusOff($contact_id){
		return $this->update("contact", ["status"=>0], false, [["contact_id","=",$contact_id]]);
	}

	####################################################################################################


}
