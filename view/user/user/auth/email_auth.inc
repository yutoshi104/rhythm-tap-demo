
<form action="" method="POST" class="signin-form">
    <div class="signin-logo">
        <img src="/assets/src/logo/logo.png" alt="<?php echo SITE_NAME; ?>">
    </div>
    <h1 class="signin-title">新規登録</h1>
    <p class="signin-description">あなたのメールアドレスを入力してください。入力したメールアドレス宛に本登録用のURLをお送りいたしますので、お送りしたメールのURLをクリックいただき、30分以内に登録を完了させてください。</p>

    <?php
        if( !empty($email_auth_err) ) {
            printf('<div class="signin-alert" role="alert">%s</div>'."\n", $email_auth_err);
        }
    ?>
    
    <label for="inputEmail" class="signin-no-label">メールアドレス</label>
    <input name="email" type="email" id="inputEmail" class="signin-form-input" placeholder="メールアドレス" value="" required autofocus>

    <input name="mode" type="hidden" value="email_auth">
    <input name="csrf_token" type="hidden" value="<?php echo $csrf_token; ?>">
    <button class="signin-btn btn-green" type="submit">送信する</button>

    <p class="signin-pagelink"><a href="/login/">ログインページへ</a></p>

</form>
