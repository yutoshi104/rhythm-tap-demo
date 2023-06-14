<?php

trait UploadDb {

    ####################################################################################################
    # imageテーブル
    ####################################################################################################

    # 画像取得
    public function getImageData($image_category,$image_subcategory,$image_key_id,$image_key_id2=0){
        $res = $this->fetch("image", "*", [["image_category","=",$image_category],["image_subcategory","=",$image_subcategory],["image_key_id","=",$image_key_id],["image_key_id2","=",$image_key_id2]],["uptime","DESC"]);
        if($res){
            return $res->fetch_all(MYSQLI_ASSOC);
        }else{
            return null;
        }
    }
    # 画像件数取得
    public function getImageDataNum($image_category,$image_subcategory=null,$image_key_id=null,$image_key_id2=null){
        $where = [["image_category","=",$image_ategory]];
        if(isset($image_subcategory)){
            $where[] = ["image_subcategory","=",$image_subcategory];
        }
        if(isset($image_key_id)){
            $where[] = ["image_key_id","=",$image_key_id];
        }
        if(isset($image_key_id2)){
            $where[] = ["image_key_id2","=",$image_key_id2];
        }
        $res = $this->fetch("image", "COUNT(*) as num", $where);
        if($res){
            return $res->fetch_array(MYSQLI_ASSOC)['num'];
        }else{
            return null;
        }
    }

    # 画像登録
    public function insertImageData($image_category,$image_subcategory,$image_key_id,$image_key_id2,$image_type,$image_name,$image_path,$image_thumb_path,$image_size,$image_height,$image_width){
        return $this->insert("image", ["image_category"=>$image_category,"image_subcategory"=>$image_subcategory,"image_key_id"=>$image_key_id,"image_key_id2"=>$image_key_id2,"image_type"=>$image_type,"image_name"=>$image_name,"image_path"=>$image_path,"image_thumb_path"=>$image_thumb_path,"image_size"=>$image_size,"image_height"=>$image_height,"image_width"=>$image_width], true, true);
    }

    # 画像削除
    public function deleteImageData($image_category,$image_subcategory,$image_key_id,$image_key_id2=0){
		return $this->physicalDelete("image", [["image_category","=",$image_category],["image_subcategory","=",$image_subcategory],["image_key_id","=",$image_key_id],["image_key_id2","=",$image_key_id2]]);
    }

    ####################################################################################################


    ####################################################################################################
    # fileテーブル
    ####################################################################################################

    # ファイル取得
    public function getFileData($file_category,$file_subcategory,$file_key_id,$file_key_id2=0){
        $res = $this->fetch("file", "*", [["file_category","=",$file_category],["file_subcategory","=",$file_subcategory],["file_key_id","=",$file_key_id],["file_key_id2","=",$file_key_id2]]);
        if($res){
            return $res->fetch_all(MYSQLI_ASSOC);
        }else{
            return null;
        }
    }
    # ファイル件数取得
    public function getFileDataNum($file_category,$file_subcategory=null,$file_key_id=null,$file_key_id2=null){
        $where = [["file_category","=",$file_category]];
        if(isset($file_subcategory)){
            $where[] = ["file_subcategory","=",$file_subcategory];
        }
        if(isset($file_key_id)){
            $where[] = ["file_key_id","=",$file_key_id];
        }
        if(isset($file_key_id2)){
            $where[] = ["file_key_id2","=",$file_key_id2];
        }
        $res = $this->fetch("file", "COUNT(*) as num", $where);
        if($res){
            return $res->fetch_array(MYSQLI_ASSOC)['num'];
        }else{
            return null;
        }
    }

    # ファイル登録
    public function insertFileData($file_category,$file_subcategory,$file_key_id,$file_key_id2,$file_mime_type,$file_name,$file_path,$file_size){
        return $this->insert("file", ["file_category"=>$file_category,"file_subcategory"=>$file_subcategory,"file_key_id"=>$file_key_id,"file_key_id2"=>$file_key_id2,"file_mime_type"=>$file_mime_type,"file_name"=>$file_name,"file_path"=>$file_path,"file_size"=>$file_size], true, true);
    }

    # ファイル削除
    public function deleteFileData($file_category,$file_subcategory,$file_key_id,$file_key_id2=0){
		return $this->physicalDelete("file", [["file_category","=",$file_category],["file_subcategory","=",$file_subcategory],["file_key_id","=",$file_key_id],["file_key_id2","=",$file_key_id2]]);
    }

    ####################################################################################################


}
