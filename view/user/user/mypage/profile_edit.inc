
<div class="wrapper">
    <div class="container">
        <main>
            <h1 class="title">プロフィール編集</h1>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>プロフィール画像</label>
                    <div class="form-child form-image">
                        <div class="form-image-img">
                            <img id="preview_image" src="<?php echo $mode !== "confirm" ? $page['user_icon_url'] : '/api/get_image_content/'; ?>" class="icon-circle" style="width:30vw;height:30vw;max-width:200px;max-height:200px;min-width:180px;min-height:180px;">
                        </div>
                        <?php
                            if($mode==="edit"){
                        ?>
                            <div class="btn-group">
                                <button class="btn btn-small btn-lightblue" style="margin-bottom:0.5rem;font-size:0.9rem;" type="button" onclick="return setDefaultIcon()">デフォルト画像に戻す</button>
                                <input type="hidden" name="default_icon" value="0">
                            </div>
                            <div id="drop_area" class="form-file">
                                <p>ここにファイルをドロップしてください。または</p>
                                <div class="form-file-input">
                                    <input type="file" id="input_file" name="image" accept=".png, .jpg, .jpeg, .gif" onchange="inputFile(this)">
                                    <button id="input_button" class="btn btn-normal btn-gray" type="button" onchange="deleteImage()">ファイルを選択する</button>
                                </div>
                                <span id="input_detail">選択されていません</span>
                                <button id="del_input_button" class="btn btn-normal btn-red" type="button" onclick="deleteFile()">取消</button>
                            </div>
                        <?php
                            }
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputName" class="required">氏名</label>
                    <?php
                        if($mode==="edit"){
                            printf('<input class="form-input" name="user_name" type="text" id="inputName" placeholder="%d文字以内" maxlength="%d" value="%s" required>'."\n", MAX_NAME, MAX_NAME, isset($user_name) ? h($user_name) : '');
                            printf('<small id="error_name" class="error-small">%s</small>'."\n", isset($edit_err['user_name']) ? implode("",$edit_err['user_name']) : "");
                        }else{
                            printf('<div class="form-confirm">%s</div>'."\n", isset($user_name) ? h($user_name) : '');
                            printf('<input name="user_name" type="hidden" id="inputName" value="%s">'."\n", isset($user_name) ? h($user_name) : '');
                        }
                    ?>
                </div>
                <div class="form-group">
                    <label for="inputKey" class="required"><span>ユーザーID<?php echo $mode==="edit" ? "<br>(半角英数字と「_」のみ使用可能)" : ""; ?></span></label>
                    <?php
                        if($mode==="edit"){
                            printf('<input class="form-input" name="user_key" type="text" id="inputKey" placeholder="%d文字以内" maxlength="%d" value="%s" required>'."\n", MAX_USERKEY, MAX_USERKEY, isset($user_key) ? h($user_key) : '');
                            printf('<small id="error_key" class="error-small">%s</small>'."\n", isset($edit_err['user_key']) ? implode("",$edit_err['user_key']) : "");
                        }else{
                            printf('<div class="form-confirm">%s</div>'."\n", isset($user_key) ? h($user_key) : '');
                            printf('<input name="user_key" type="hidden" id="inputKey" value="%s">'."\n", isset($user_key) ? h($user_key) : '');
                        }
                    ?>
                </div>
                <div class="form-group">
                    <label for="inputNickName" class="required">ニックネーム</label>
                    <?php
                        if($mode==="edit"){
                            printf('<input class="form-input" name="nickname" type="text" id="inputNickName" placeholder="%d文字以内" maxlength="%d" value="%s" required>'."\n", MAX_NICKNAME, MAX_NICKNAME, isset($nickname) ? h($nickname) : '');
                            printf('<small id="error_nickname" class="error-small">%s</small>'."\n", isset($edit_err['nickname']) ? implode("",$edit_err['nickname']) : "");
                        }else{
                            printf('<div class="form-confirm">%s</div>'."\n", isset($nickname) ? h($nickname) : '');
                            printf('<input name="nickname" type="hidden" id="inputNickName" value="%s">'."\n", isset($nickname) ? h($nickname) : '');
                        }
                    ?>
                </div>
                <?php
                    if($mode==="edit"){
                        printf('<div class="form-group">'."\n");
                        printf('<label for="inputPassword" class="required">パスワード</label>'."\n");
                        printf('<div class="form-child">'."\n");
                        printf('<p>パスワード変更はパスワード忘れページよりお願いします</p>'."\n");
                        printf('<div class="btn-group">'."\n");
                        printf('<button id="submit_button" class="btn btn-small btn-yellow" type="button" onclick="location.href=\'/forgot\'">パスワード変更ページへ</button>'."\n");
                        printf('</div>'."\n");
                        printf('</div>'."\n");
                        printf('</div>'."\n");
                    }
                ?>

                <input name="mode" type="hidden" value="<?php echo $mode; ?>">
                <input name="csrf_token" type="hidden" value="<?php echo $csrf_token; ?>">

                <div class="btn-group"><button id="submit_button" class="btn btn-normal btn-<?php echo $mode==="edit" ? "green" : "blue"; ?>" type="submit" style="max-width:300px;"><?php echo $mode==="edit" ? "確認" : "送信"; ?></button></div>

                <?php
                    if($mode === "confirm"){
                        printf('<div class="btn-group">'."\n");
                        printf('<button class="btn btn-normal btn-gray" style="margin-top:0.5rem;" type="submit" onclick="return setModeBack()">編集を続ける</button>'."\n");
                        printf('</div>'."\n");
                    }
                ?>

                <p class="signin-pagelink"><a href="/mypage/">マイページへ戻る</a></p>
            </form>
        </main>
    </div>
</div>

<?php
    if($mode==="edit"){
?>
<script>
    var instance1 = new JSValidation("text","inputName","error_name");
    instance1.setValidation("required",true);
    instance1.setValidation("minlength",1);
    instance1.setValidation("maxlength",<?php echo MAX_NAME; ?>);
    var instance2 = new JSValidation("text","inputKey","error_key");
    instance2.setValidation("required",true);
    instance2.setValidation("regexp",/^[0-9A-Za-z_]+$/);
    instance2.setValidation("minlength",1);
    instance2.setValidation("maxlength",<?php echo MAX_USERKEY; ?>);
    //サーバーと通信して使用済みのnicknameが確かめる(custom_func)
    instance2.setValidation("custom_func",function(value){
        let xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/check_user_id/', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.responseType = 'json';
        let json = JSON.stringify({ "user_id" : value });
        xhr.onreadystatechange = function(){
            if(this.readyState == 4 && this.status == 200){
                if(!xhr.response.result){
                    document.getElementById("error_key").innerHTML = xhr.response.message;
                }
            }
        }
        xhr.send(json);
        return null;
    });
    var instance3 = new JSValidation("text","inputNickName","error_nickname");
    instance3.setValidation("required",true);
    instance3.setValidation("minlength",1);
    instance3.setValidation("maxlength",<?php echo MAX_NICKNAME; ?>);
</script>
<?php
    }
?>

<script>
    window.default_icon_url = "<?php echo USER_ICON_DEFAULT; ?>";
</script>
