
<form action="" method="POST" class="signin-form">
    <div class="signin-logo">
        <img src="/assets/src/logo/logo.png" alt="<?php echo SITE_NAME; ?>">
    </div>
    <h1 class="signin-title">パスワード再設定</h1>
    
    <?php
        if( !empty($reset_err) ) {
            printf('<div class="signin-alert" role="alert">%s</div>'."\n", $reset_err);
        }
    ?>
    
    <label for="inputPassword" class="signin-no-label">パスワード</label>
    <input name="password" type="password" id="inputPassword" class="signin-form-input" placeholder="パスワード" value="" required autofocus>
    <label for="inputPasswordConf" class="signin-no-label">パスワード再入力</label>
    <input name="password_conf" type="password" id="inputPasswordConf" class="signin-form-input" placeholder="パスワード再入力" required>
    
    <input name="mode" type="hidden" value="reset">
    <input name="csrf_token" type="hidden" value="<?php echo $csrf_token; ?>">
    <button class="signin-btn btn-green" type="submit">再設定</button>

</form>
