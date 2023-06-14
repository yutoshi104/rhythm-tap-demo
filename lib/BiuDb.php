<?php

trait BiuDb {

    ####################################################################################################
    # biuテーブル
    ####################################################################################################

    # Biu取得
    public function getBiuData($key){
        $res = $this->fetch("biu", "*", [["biu_key","=",$key]]);
        if($res){
            return $res->fetch_array(MYSQLI_ASSOC);
        }else{
            return null;
        }
    }
    # Biu取得(valueのみ)
    public function getBiuValue($key){
        $res = $this->fetch("biu", "biu_value", [["biu_key","=",$key]]);
        if($res){
            return $res->fetch_array(MYSQLI_ASSOC)['biu_value'];
        }else{
            return null;
        }
    }


    # Biu登録
    public function insertBiuData($biu_key,$biu_value){
        return $this->insert("biu", ["biu_key"=>$biu_key,"biu_value"=>$biu_value], true, true);
    }

    # Biu更新
    public function updateBiuData($biu_key,$biu_value){
        return $this->update("biu", ["biu_value"=>$biu_value], true, [["biu_key","=",$biu_key]]);
    }
    # Biu削除
    public function deleteBiuData($biu_key){
		return $this->physicalDelete("biu", [["biu_key","=",$biu_key]]);
    }

    ####################################################################################################


}
