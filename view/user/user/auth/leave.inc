
<div class="wrapper" style="background-color:white;">
    <div class="container">
        <main>
            <form action="" method="POST">
                <div class="signin-logo">
                    <img src="/assets/src/logo/logo.png" alt="<?php echo SITE_NAME; ?>">
                </div>
                <h1 class="signin-title">退会申請</h1>
                
                <div class="signin-alert" role="alert">
                    <h4>退会する際の注意点<h4>
                    <ul>
                        <li>一度退会すると、同じユーザーネームやメールアドレスでは登録ができなくなります。</li>
                        <li>運営側でも、退会されたアカウントを再開することはできません。</li>
                        <li>退会しても、これまでに作成した対戦記録などは削除されません。</li>
                    </ul>
                </div>

                <?php
                    if( !empty($leave_err) ) {
                        printf('<div class="signin-alert" role="alert">%s</div>'."\n", $leave_err);
                    }
                ?>

                <?php
                    if($mode==="leave"){
                        printf('<label for="leave_reason" style="margin-top:2rem;">大変お手数ですが、サービス向上のために退会理由アンケートのご協力お願いいたします。</label>'."\n");
                        printf('<div class="form-radio-area">'."\n");
                        foreach(LEAVE_REASON as $key=>$value){
                            printf('<div class="form-radio"><input type="radio" id="d%d" name="leave_reason" value="%d"%s><label for="d%d">%s</label></div>'."\n", h($key), h($key), (int)$key===(int)$leave_reason ? " checked" : "", h($key), h($value));
                        }
                        printf('</div>'."\n");
                    }else{
                        printf('<div class="form-group">'."\n");
                        printf('<label for="leave_reason">退会理由</label>'."\n");
                        printf('<div class="form-confirm">%s</div>'."\n", isset($leave_reason) ? h(LEAVE_REASON[(int)$leave_reason]) : '<span class="text-gray">未選択</span>');
                        printf('<input name="leave_reason" type="hidden" id="leave_reason" value="%s">'."\n", isset($leave_reason) ? h(LEAVE_REASON[(int)$leave_reason]) : '');
                        printf('</div>'."\n");
                    }
                ?>
                <?php
                    if($mode==="leave"){
                        printf('<label for="leave_detail" style="margin-top:2rem;">ご意見などございましたら、こちらにご記入ください。</label>'."\n");
                        printf('<textarea class="signin-form-input" name="leave_detail" rows="10" style="resize:vertical;" maxlength="%d">%s</textarea>'."\n", MAX_CONTACT_DETAIL, isset($leave_detail) ? h($leave_detail) : '');
                    }else{
                        printf('<div class="form-group">'."\n");
                        printf('<label for="leave_detail">ご意見</label>'."\n");
                        printf('<div class="form-confirm">%s</div>'."\n", isset($leave_detail) ? h($leave_detail) : '<span class="text-gray">未記入</span>');
                        printf('<input name="leave_detail" type="hidden" id="leave_detail" value="%s">'."\n", isset($leave_detail) ? h($leave_detail) : '');
                        printf('</div>'."\n");
                    }
                ?>
                <?php
                    if($mode==="confirm"){
                        printf('<label for="inputPassword">アカウントのパスワード</label>'."\n");
                        printf('<input name="password" type="password" id="inputPassword" class="signin-form-input" placeholder="アカウントのパスワード" required>'."\n");
                    }
                ?>

                <input name="mode" type="hidden" value="<?php echo $mode; ?>">
                <input name="csrf_token" type="hidden" value="<?php echo $csrf_token; ?>">

                <?php
                    if($mode==="confirm"){
                        printf('<div class="g-recaptcha" data-sitekey="%s" data-callback="clearcall"></div>'."\n", G_RECAPTCHA_SITE_KEY);
                    }
                ?>

                <button id="submit_button" class="signin-btn btn-red" type="submit"<?php echo $mode==="confirm" ? " disabled" : ""; ?>><?php echo $mode==="confirm" ? "確認して退会する" : "退会する"; ?></button></div>
                <?php
                    if($mode==="confirm"){
                        printf('<button class="signin-btn btn-gray" type="button" onclick="history.back()">編集を続ける</button></div>'."\n");
                    }
                ?>
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