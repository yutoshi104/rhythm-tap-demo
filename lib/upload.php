<?php

####################################################################################################
# 画像アップロード
####################################################################################################

function insertImageData($db,$src,$image_name,$image_category,$image_subcategory,$image_key_id,$image_key_id2=0){
    # tmpとして別に保存してある$srcを保存($_FILESから直接は不可)

    # 画像情報取得
    $image_name = basename($image_name);
    $img_info = getimagesize($src);
    $path_info = pathinfo($image_name);
    $image_type = exif_imagetype($src);
    if(!array_key_exists($image_type,IMG_TYPE_LIST)){
        return false;
    }
    $extension = $path_info['extension'];
    $image_size = filesize($src);
    $image_width = !empty($img_info[0]) ? $img_info[0] : 0;
    $image_height = !empty($img_info[1]) ? $img_info[1] : 0;

    # パス取得
    $dst_dir = sprintf("%s/%03d/%03d/%08d/%08d/", 
        UPLOAD_IMAGE_DIR, $image_category, $image_subcategory, $image_key_id, $image_key_id2
    );
    $key = "";
    for($i = 0; $i < 10; $i++) {
		$tmp = makeRandomKey(12);
		if(!is_file($dst_dir.$tmp.".".$extension) && !is_file($dst_dir.$tmp."_thumb.jpg")) {
			$key = $tmp;
			break;
		}
	}
    if($key === "") {
		return false;
	}
    $dst_path = $dst_dir.$key.".".$extension;
    $dst_thumb_path = $dst_dir.$key."_thumb.jpg";

    # ディレクトリ作成
    if(!is_dir(dirname($dst_path))) {
		if(!mkdir(dirname($dst_path), 0777, true)) {
			return false;
		}
	}
    
    # 画像保存
    if(!rename($src,$dst_path)){
        return false;
    }
    if(!chmod($dst_path, 0664)){
        return false;
    }

    # サムネイル保存
    // if($image_width > $image_height){
    //     $w = 400;
    //     $h = floor($image_height/($image_width/400));
    // }else{
    //     $w = floor($image_width/($image_height/400));
    //     $h = 400;
    // }
    // switch ($image_type) {
    //     case IMAGETYPE_JPEG:
    //         $original_image = imagecreatefromjpeg($dst_path);
    //         $canvas = imagecreatetruecolor($w, $h);
    //         imagecopyresampled($canvas, $original_image, 0,0,0,0, $w, $h, $image_width, $image_height);
    //         imagejpeg($canvas, $dst_thumb_path);
    //         break;
    //     case IMAGETYPE_PNG:
    //         $original_image = imagecreatefrompng($dst_path);
    //         $canvas = imagecreatetruecolor($w, $h);
    //         imagecopyresampled($canvas, $original_image, 0,0,0,0, $w, $h, $image_width, $image_height);
    //         imagepng($canvas, $dst_thumb_path);
    //         break;
    //     case IMAGETYPE_GIF:
    //         $original_image = imagecreatefromgif($dst_path);
    //         $canvas = imagecreatetruecolor($w, $h);
    //         imagecopyresampled($canvas, $original_image, 0,0,0,0, $w, $h, $image_width, $image_height);
    //         imagegif($canvas, $dst_thumb_path);
    //         break;
    //     default:
    //         return false;
    //         throw new RuntimeException('対応していないファイル形式です。: ', $type);
    // }
    // imagedestroy($canvas);
    // imagedestroy($original_image);

    // $cmd = "";
    // if($image_width > $image_height){
    //     $cmd = sprintf("convert -resize 400x %s %s", $dst_path, $dst_thumb_path);
    // }else{
    //     $cmd = sprintf("convert -resize x400 %s %s", $dst_path, $dst_thumb_path);
    // }
    // exec($cmd);
    // if(!chmod($dst_thumb_path, 0664)){
    //     return false;
    // }

    # データ登録
    $image_path = str_replace(PUBLIC_DIR,"",$dst_path);
    $image_thumb_path = str_replace(PUBLIC_DIR,"",$dst_thumb_path);
    if(!$db->insertImageData($image_category,$image_subcategory,$image_key_id,$image_key_id2,$image_type,$image_name,$image_path,$image_thumb_path,$image_size,$image_height,$image_width)){
        return false;
    }
    return true;
}

function insertImageDataUrl($db,$url,$image_category,$image_subcategory,$image_key_id,$image_key_id2=0){

    # 画像情報取得
    $img = file_get_contents($url);
    $image_name = basename($url);
    $img_info = getimagesize($url);
    $path_info = pathinfo($image_name);
    $image_type = exif_imagetype($url);
    if(!array_key_exists($image_type,IMG_TYPE_LIST)){
        return false;
    }
    $extension = $path_info['extension'];
    $image_size = strlen($img);
    $image_width = !empty($img_info[0]) ? $img_info[0] : 0;
    $image_height = !empty($img_info[1]) ? $img_info[1] : 0;

    # パス取得
    $dst_dir = sprintf("%s/%03d/%03d/%08d/%08d/", 
        UPLOAD_IMAGE_DIR, $image_category, $image_subcategory, $image_key_id, $image_key_id2
    );
    $key = "";
    for($i = 0; $i < 10; $i++){
		$tmp = makeRandomKey(12);
		if( !is_file($dst_dir.$tmp.".".$extension) && !is_file($dst_dir.$tmp."_thumb.jpg") ){
			$key = $tmp;
			break;
		}
	}
    if($key === "") {
		return false;
	}
    $dst_path = $dst_dir.$key.".".$extension;
    $dst_thumb_path = $dst_dir.$key."_thumb.jpg";

    # ディレクトリ作成
    if(!is_dir(dirname($dst_path))){
		if(!mkdir(dirname($dst_path), 0777, true)){
			return false;
		}
	}
    
    # 画像保存
    if(!file_put_contents($dst_path,$img)){
        return false;
    }
    if(!chmod($dst_path, 0664)){
        return false;
    }

    # サムネイル保存
    // $cmd = "";
    // if($image_width > $image_height){
    //     $cmd = sprintf("convert -resize 400x %s %s", $dst_path, $dst_thumb_path);
    // }else{
    //     $cmd = sprintf("convert -resize x400 %s %s", $dst_path, $dst_thumb_path);
    // }
    // exec($cmd);
    // if(!chmod($dst_thumb_path, 0664)){
    //     return false;
    // }

    # データ登録
    $image_path = str_replace(PUBLIC_DIR,"",$dst_path);
    $image_thumb_path = str_replace(PUBLIC_DIR,"",$dst_thumb_path);
    if(!$db->insertImageData($image_category,$image_subcategory,$image_key_id,$image_key_id2,$image_type,$image_name,$image_path,$image_thumb_path,$image_size,$image_height,$image_width)){
        return false;
    }
    return true;
}

function insertImageDataContent($db,$img_content,$img_name,$img_type,$img_info,$image_category,$image_subcategory,$image_key_id,$image_key_id2=0){

    # 画像情報取得
    $img = $img_content;
    $image_name = basename($img_name);
    $path_info = pathinfo($image_name);
    $image_type = $img_type;
    if(!array_key_exists($image_type,IMG_TYPE_LIST)){
        return false;
    }
    $extension = $path_info['extension'];
    $image_size = strlen($img);
    $image_width = !empty($img_info[0]) ? $img_info[0] : 0;
    $image_height = !empty($img_info[1]) ? $img_info[1] : 0;

    # パス取得
    $dst_dir = sprintf("%s/%03d/%03d/%08d/%08d/", 
        UPLOAD_IMAGE_DIR, $image_category, $image_subcategory, $image_key_id, $image_key_id2
    );
    $key = "";
    for($i = 0; $i < 10; $i++){
		$tmp = makeRandomKey(12);
		if( !is_file($dst_dir.$tmp.".".$extension) && !is_file($dst_dir.$tmp."_thumb.jpg") ){
			$key = $tmp;
			break;
		}
	}
    if($key === "") {
		return false;
	}
    $dst_path = $dst_dir.$key.".".$extension;
    $dst_thumb_path = $dst_dir.$key."_thumb.jpg";

    # ディレクトリ作成
    if(!is_dir(dirname($dst_path))){
		if(!mkdir(dirname($dst_path), 0777, true)){
			return false;
		}
	}
    
    # 画像保存
    if(!file_put_contents($dst_path,$img)){
        return false;
    }
    if(!chmod($dst_path, 0664)){
        return false;
    }

    # サムネイル保存
    // $cmd = "";
    // if($image_width > $image_height){
    //     $cmd = sprintf("convert -resize 400x %s %s", $dst_path, $dst_thumb_path);
    // }else{
    //     $cmd = sprintf("convert -resize x400 %s %s", $dst_path, $dst_thumb_path);
    // }
    // exec($cmd);
    // if(!chmod($dst_thumb_path, 0664)){
    //     return false;
    // }

    # データ登録
    $image_path = str_replace(PUBLIC_DIR,"",$dst_path);
    $image_thumb_path = str_replace(PUBLIC_DIR,"",$dst_thumb_path);
    if(!$db->insertImageData($image_category,$image_subcategory,$image_key_id,$image_key_id2,$image_type,$image_name,$image_path,$image_thumb_path,$image_size,$image_height,$image_width)){
        return false;
    }
    return true;
}

function deleteImageData($db,$image_category,$image_subcategory,$image_key_id,$image_key_id2=0){

    $image_data = $db->getImageData($image_category,$image_subcategory,$image_key_id,$image_key_id2);
    if(isset($image_data)){

        # 画像削除
        foreach($image_data as $i){
            if(file_exists(PUBLIC_DIR.$i['image_path'])){
                if(!unlink(PUBLIC_DIR.$i['image_path'])){
                    return false;
                }
            }
            if(file_exists(PUBLIC_DIR.$i['image_thumb_path'])){
                if(!unlink(PUBLIC_DIR.$i['image_thumb_path'])){
                    return false;
                }
            }
        }

        # データ削除
        if(!$db->deleteImageData($image_category,$image_subcategory,$image_key_id,$image_key_id2)){
            return false;
        }
    }
    return true;
}

function uploadTmpImg($src){

	$image_type = exif_imagetype($src);
    # 拡張子チェック
	$prefix = "";
	if( $image_type !== false && isset(IMG_TYPE_LIST[$image_type]) ) {
		$prefix = "." . IMG_TYPE_LIST[$image_type];
	} else {
		return false;
	}

    # ディレクトリ作成
    if(!is_dir(dirname(UPLOAD_TMP_DIR))){
        if(!mkdir(dirname(UPLOAD_TMP_DIR), 0777, true)){
            return false;
        }
    }

    # ランダムキー生成
	$dst = UPLOAD_TMP_DIR;
	$key = "";
	for( $i = 0; $i < 10; $i++ ) {
		$tmp = makeRandomKey(12);
		if( !is_file($dst."/".$tmp.$prefix) ) {
			$key = $tmp;
			break;
		}
	}
	if( $key === "" ) {
		return false;
	}

    # tmpファイルアップロード
	$dst .= "/".$key.$prefix;
	if( copy($src, $dst) === false ) {
		return false;
	}
    if(!chmod($dst, 0664)){
        return false;
    }

	return $dst;
}

####################################################################################################


####################################################################################################
# fileテーブル
####################################################################################################

function insertFileData($db,$src,$file_name,$file_category,$file_subcategory,$file_key_id,$file_key_id2=0){
    
    # ファイル情報取得
    $file_name = basename($file_name);
    $path_info = pathinfo($file_name);
    $file_mime_type = mime_content_type($src);
    $extension = $path_info['extension'];
    $file_size = filesize($src);

    # パス取得
    $dst_dir = sprintf("%s/%03d/%03d/%08d/%08d/", 
        UPLOAD_FILE_DIR, $file_category, $file_subcategory, $file_key_id, $file_key_id2
    );
    $key = "";
    for($i = 0; $i < 10; $i++){
		$tmp = makeRandomKey(12);
		if(!is_file($dst_dir.$tmp.".".$extension)){
			$key = $tmp;
			break;
		}
	}
    if($key === "") {
		return false;
	}
    $dst_path = $dst_dir.$key.".".$extension;

    # ディレクトリ作成
    if(!is_dir(dirname($dst_path))){
		if(!mkdir(dirname($dst_path), 0777, true)){
			return false;
		}
	}
    
    # ファイル保存
    if(!rename($src,$dst_path)){
        return false;
    }
    if(!chmod($dst_path, 0664)){
        return false;
    }

    # データ登録
    $file_path = str_replace(PUBLIC_DIR,"",$dst_path);
    if(!$db->insertFileData($file_category,$file_subcategory,$file_key_id,$file_key_id2,$file_mime_type,$file_name,$file_path,$file_size)){
        return false;
    }
    return true;
}

function insertFileDataUrl($db,$url,$file_category,$file_subcategory,$file_key_id,$file_key_id2=0){

    # ファイル情報取得
    $file = file_get_contents($url);
    $file_name = basename($url);
    $path_info = pathinfo($file_name);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $file_mime_type = $finfo->buffer($file);
    // $file_mime_type = mime_content_type($url);
    $extension = $path_info['extension'];
    $file_size = strlen($file);

    # パス取得
    $dst_dir = sprintf("%s/%03d/%03d/%08d/%08d/", 
        UPLOAD_FILE_DIR, $file_category, $file_subcategory, $file_key_id, $file_key_id2
    );
    $key = "";
    for($i = 0; $i < 10; $i++){
		$tmp = makeRandomKey(12);
		if(!is_file($dst_dir.$tmp.".".$extension)){
			$key = $tmp;
			break;
		}
	}
    if($key === ""){
		return false;
	}
    $dst_path = $dst_dir.$key.".".$extension;

    # ディレクトリ作成
    if(!is_dir(dirname($dst_path))){
		if(!mkdir(dirname($dst_path), 0777, true)){
			return false;
		}
	}
    
    # ファイル保存
    if(!file_put_contents($dst_path,$file)){
        return false;
    }
    if(!chmod($dst_path, 0664)){
        return false;
    }

    # データ登録
    $file_path = str_replace(PUBLIC_DIR,"",$dst_path);
    if(!$db->insertFileData($file_category,$file_subcategory,$file_key_id,$file_key_id2,$file_mime_type,$file_name,$file_path,$file_size)){
        return false;
    }
    return true;
}

function insertFileDataContect($db,$file_content,$file_name,$file_category,$file_subcategory,$file_key_id,$file_key_id2=0){

    # ファイル情報取得
    $file = $file_content;
    $file_name = basename($file_name);
    $path_info = pathinfo($file_name);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $file_mime_type = $finfo->buffer($file);
    // $file_mime_type = mime_content_type($url);
    $extension = $path_info['extension'];
    $file_size = strlen($file);

    # パス取得
    $dst_dir = sprintf("%s/%03d/%03d/%08d/%08d/", 
        UPLOAD_FILE_DIR, $file_category, $file_subcategory, $file_key_id, $file_key_id2
    );
    $key = "";
    for($i = 0; $i < 10; $i++){
		$tmp = makeRandomKey(12);
		if(!is_file($dst_dir.$tmp.".".$extension)){
			$key = $tmp;
			break;
		}
	}
    if($key === ""){
		return false;
	}
    $dst_path = $dst_dir.$key.".".$extension;

    # ディレクトリ作成
    if(!is_dir(dirname($dst_path))){
		if(!mkdir(dirname($dst_path), 0777, true)){
			return false;
		}
	}
    
    # ファイル保存
    if(!file_put_contents($dst_path,$file)){
        return false;
    }
    if(!chmod($dst_path, 0664)){
        return false;
    }

    # データ登録
    $file_path = str_replace(PUBLIC_DIR,"",$dst_path);
    if(!$db->insertFileData($file_category,$file_subcategory,$file_key_id,$file_key_id2,$file_mime_type,$file_name,$file_path,$file_size)){
        return false;
    }
    return true;
}

function deleteFileData($db,$file_category,$file_subcategory,$file_key_id,$file_key_id2=0){

    $file_data = $db->getFileData($file_category,$file_subcategory,$file_key_id,$file_key_id2);
    if(isset($file_data)){

        # ファイル削除
        foreach($file_data as $f){
            if(file_exists(PUBLIC_DIR.$f['file_path'])){
                if(!unlink(PUBLIC_DIR.$f['file_path'])) {
                    return false;
                }
            }
        }

        # データ削除
        if(!$db->deleteFileData($file_category,$file_subcategory,$file_key_id,$file_key_id2)){
            return false;
        }
    }
    return true;
}


####################################################################################################




####################################################################################################
# ユーザーアイコン取得
####################################################################################################

function getUserIcon($db,$user_id=null){
    if(isset($user_id) && $icon_data = $db->getImageData(IMG_CATE_USER,IMG_SUBCATE_USER_ICON,$user_id)){
        if(file_exists(PUBLIC_DIR.$icon_data[0]['image_path'])){
            $user_icon_url = $icon_data[0]['image_path'];
            $user_icon_url_thumb = $icon_data[0]['image_thumb_path'];
        }else{
            $user_icon_url = USER_ICON_DEFAULT;
            $user_icon_url_thumb = USER_ICON_DEFAULT;
        }
    }else{
        $user_icon_url = USER_ICON_DEFAULT;
        $user_icon_url_thumb = USER_ICON_DEFAULT;
    }
    return [$user_icon_url,$user_icon_url_thumb];
}

####################################################################################################

