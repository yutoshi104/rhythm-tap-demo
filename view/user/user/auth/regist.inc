
<div class="wrapper">
    <div class="container">
        <main>
            <h1 class="title">新規登録</h1>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>プロフィール画像</label>
                    <div class="form-child form-image">
                        <div class="form-image-img">
                            <img id="preview_image" src="<?php echo $mode==="regist" ? USER_ICON_DEFAULT : '/api/get_image_content/'; ?>" class="icon-circle" style="width:30vw;height:30vw;max-width:200px;max-height:200px;min-width:180px;min-height:180px;">
                        </div>
                        <?php
                            if($mode==="regist"){
                        ?>
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
                        if($mode==="regist"){
                            printf('<input class="form-input" name="user_name" type="text" id="inputName" placeholder="%d文字以内" maxlength="%d" value="%s" required>'."\n", MAX_NAME, MAX_NAME, isset($user_name) ? h($user_name) : '');
                            printf('<small id="error_name" class="error-small">%s</small>'."\n", isset($regist_err['user_name']) ? implode("",$regist_err['user_name']) : "");
                        }else{
                            printf('<div class="form-confirm">%s</div>'."\n", isset($user_name) ? h($user_name) : '');
                            printf('<input name="user_name" type="hidden" id="inputName" value="%s">'."\n", isset($user_name) ? h($user_name) : '');
                        }
                    ?>
                </div>
                <div class="form-group">
                    <label for="inputKey" class="required"><span>ユーザーID<?php echo $mode==="regist" ? "<br>(半角英数字と「_」のみ使用可能)" : ""; ?></span></label>
                    <?php
                        if($mode==="regist"){
                            printf('<input class="form-input" name="user_key" type="text" id="inputKey" placeholder="%d文字以内" maxlength="%d" value="%s" required>'."\n", MAX_USERKEY, MAX_USERKEY, isset($user_key) ? h($user_key) : '');
                            printf('<small id="error_key" class="error-small">%s</small>'."\n", isset($regist_err['user_key']) ? implode("",$regist_err['user_key']) : "");
                        }else{
                            printf('<div class="form-confirm">%s</div>'."\n", isset($user_key) ? h($user_key) : '');
                            printf('<input name="user_key" type="hidden" id="inputKey" value="%s">'."\n", isset($user_key) ? h($user_key) : '');
                        }
                    ?>
                </div>
                <div class="form-group">
                    <label for="inputNickName" class="required">ニックネーム</label>
                    <?php
                        if($mode==="regist"){
                            printf('<input class="form-input" name="nickname" type="text" id="inputNickName" placeholder="%d文字以内" maxlength="%d" value="%s" required>'."\n", MAX_NICKNAME, MAX_NICKNAME, isset($nickname) ? h($nickname) : '');
                            printf('<small id="error_nickname" class="error-small">%s</small>'."\n", isset($regist_err['nickname']) ? implode("",$regist_err['nickname']) : "");
                        }else{
                            printf('<div class="form-confirm">%s</div>'."\n", isset($nickname) ? h($nickname) : '');
                            printf('<input name="nickname" type="hidden" id="inputNickName" value="%s">'."\n", isset($nickname) ? h($nickname) : '');
                        }
                    ?>
                </div>
                <div class="form-group">
                    <label for="inputEmail" class="required">メールアドレス</label>
                    <?php
                        printf('<div class="form-confirm">%s</div>'."\n", isset($email) ? h($email) : '');
                        printf('<input name="email" type="hidden" id="inputEmail" value="%s">'."\n", isset($email) ? h($email) : '');
                    ?>
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="required">パスワード</label>
                    <?php
                        if($mode==="regist"){
                            printf('<input class="form-input" name="password" type="password" id="inputPassword" placeholder="パスワード" oninput="setPasswordLevel(this.value)" maxlength="%d" required>'."\n", MAX_PASSWORD);
                            printf('<small id="error_password" class="error-small">%s</small>'."\n", isset($regist_err['password']) ? implode("",$regist_err['password']) : "");
                        }else{
                            printf('<div class="form-confirm">**********</div>'."\n");
                            printf('<input name="password" type="hidden" id="inputPassword" value="%s">'."\n", isset($password) ? h($password) : '');
                        }
                    ?>
                </div>
                <?php
                    if($mode==="regist"){
                        printf('<div class="form-group">'."\n");
                        printf('<label for="inputPassword" class="required">パスワード再入力</label>'."\n");
                        printf('<input class="form-input" name="password_conf" type="password" id="inputPasswordConf" placeholder="パスワード再入力" maxlength="%d" required>'."\n", MAX_PASSWORD);
                        printf('<small id="error_password_conf" class="error-small"></small>'."\n");
                        printf('</div>'."\n");
                    }
                ?>

                <input name="mode" type="hidden" value="<?php echo $mode; ?>">
                <input name="csrf_token" type="hidden" value="<?php echo $csrf_token; ?>">
                <?php
                    if($mode==="regist"){
                        printf('<div class="g-recaptcha" data-sitekey="%s" data-callback="clearcall"></div>'."\n", G_RECAPTCHA_SITE_KEY);
                        printf('<script src="https://www.google.com/recaptcha/api.js" async defer></script>'."\n");
                    }
                ?>
                <!--
                <p class="signin-pagelink"><a href="/terms/">利用規約</a>および<a href="/privacy/">プライバシーポリシー</a>に関する要項を確認の上、同意していただける方は「同意して新規登録」ボタンを押してください。</p>
                -->
                <div class="btn-group"><button id="submit_button" class="btn btn-normal btn-<?php echo $mode==="regist" ? "green" : "blue"; ?>" type="submit" style="max-width:300px;"<?php echo $mode==="regist" && !(defined('G_RECAPTCHA_NOCHECK') && G_RECAPTCHA_NOCHECK===true) ? " disabled" : "" ?>><?php echo $mode==="regist" ? "確認" : "送信"; ?></button></div>
                <p class="signin-pagelink"><a href="/login/">ログインページへ</a></p>
            </form>
        </main>
    </div>
</div>
<script>
    function clearcall(code) {
        if(code !== ""){
            document.getElementById('submit_button').disabled = false;
        }
    }
</script>

<?php
    if($mode==="regist"){
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
    var instance4_1 = new JSValidation("text","inputPassword","error_password");
    instance4_1.setValidation("required",true);
    instance4_1.setValidation("regexp",/^(?=.*?[a-z])(?=.*?\d)[a-z\d]+$/);
    instance4_1.setValidation("minlength",<?php echo MIN_PASSWORD; ?>);
    instance4_1.setValidation("maxlength",<?php echo MAX_PASSWORD; ?>);
    instance4_1.setMessage("regexp","パスワードは英数字をそれぞれ1文字以上含んでください。");
    var instance4_2 = new JSValidation("text","inputPasswordConf","error_password");
    instance4_2.setValidation("required",true);
    instance4_2.setValidation("custom_func",function(value){
        var pswd = document.querySelector("#inputPassword").value;
        if(pswd===""){
            return "再入力の前にパスワードを入力してください。";
        }
        if(pswd!==value){
            return "パスワードが一致しません。";
        }
        return true;
    });
</script>
<?php
    }
?>

