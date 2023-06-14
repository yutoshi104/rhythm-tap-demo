
<form action="" method="POST" class="signin-form">
    <div class="signin-logo">
        <img src="/assets/src/logo/logo.png" alt="<?php echo SITE_NAME; ?>">
    </div>
    <h1 class="signin-title">ログイン</h1>
    
    <?php
        if( !empty($login_msg) ) {
            printf('<div class="signin-message" role="alert">%s</div>'."\n", $login_msg);
        }
        if( !empty($login_err) ) {
            printf('<div class="signin-alert" role="alert">%s</div>'."\n", $login_err);
        }
    ?>
    
    <label for="inputEmail" class="signin-no-label">メールアドレス</label>
    <input name="email" type="email" id="inputEmail" class="signin-form-input" placeholder="メールアドレス" value="" required autofocus>
    <label for="inputPassword" class="signin-no-label">パスワード</label>
    <input name="password" type="password" id="inputPassword" class="signin-form-input" placeholder="パスワード" required>
    
    <input name="mode" type="hidden" value="login">
    <input name="csrf_token" type="hidden" value="<?php echo $csrf_token; ?>">
    <button class="signin-btn btn-green" type="submit">ログイン</button>

    <p class="signin-pagelink"><a href="/forgot/">パスワードをお忘れの方</a></p>
    <p class="signin-pagelink"><a href="/regist/">新規登録ページへ</a></p>

</form>
