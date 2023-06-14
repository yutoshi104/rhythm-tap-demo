
<div class="wrapper">
    <div class="container">
        <main>
            <h1 class="title">「<?php echo h($data["user_nickname"]); ?>」のマイページ</h1>
            <div class="block block-white">
                <div class="block-body">
                    <div class="block-contents">
                        <div class="form-group">
                            <label>プロフィール画像</label>
                            <div class="form-child form-image">
                                <div class="form-image-img">
                                    <img id="preview_image" src="<?php echo $disp_user_icon_url; ?>" class="icon-circle" style="width:30vw;height:30vw;max-width:200px;max-height:200px;min-width:180px;min-height:180px;">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName">氏名</label>
                            <div class="form-confirm"><?php echo h($data['user_name']); ?></div>
                        </div>
                        <div class="form-group">
                            <label for="inputKey"><span>ユーザーID</span></label>
                            <div class="form-confirm"><?php echo h($data['user_key']); ?></div>
                        </div>
                        <div class="form-group">
                            <label for="inputNickName">ニックネーム</label>
                            <div class="form-confirm"><?php echo h($data['user_nickname']); ?></div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail">メールアドレス</label>
                            <div class="form-confirm"><?php echo hideEmail(h($data['user_email'])); ?></div>
                        </div>
                    </div>
                    <hr>
                    <div class="btn-group">
                        <button class="btn btn-normal btn-green" type="button" onclick="location.href='/mypage/edit/'">プロフィール編集</button>
                    </div>
                </div>
            </div>
            <h3 class="title">参加しているグループ一覧</h3>
            <?php
                if($page['login_flg'] && $user_group_list!==[]){
                    printf('<div class="card-area">'."\n");
                    foreach($user_group_list as $ugl){
                        printf('<div class="card-vertical">'."\n");
                        printf('<div class="photo">'."\n");
                        printf('<img src="%s">'."\n", h($ugl['group_icon_url']));
                        printf('</div>'."\n");
                        printf('<ul class="details">'."\n");
                        printf('<li>%s 作成</li>'."\n", h(displayTime($ugl['intime'],23)));
                        printf('</ul>'."\n");
                        printf('<div class="description">'."\n");
                        printf('<h5 class="title-in-card">%s</h5>'."\n", h($ugl['group_name']));
                        printf('<p class="text">'."\n");
                        if(isset($ugl['group_member'])){
                            printf('グループメンバー：<br>'."\n");
                            for($i=0; $i<count($ugl['group_member']); $i++){
                                if($i===0){
                                    if($page['login_flg'] && (int)$page['user_data']['user_id']===(int)$ugl['group_member'][$i]['user_id']){
                                        printf('<span class="badge bg-lightblue">%s</span>'."\n", h($ugl['group_member'][$i]['user_nickname']));                                            
                                    }else{
                                        printf('%s'."\n", h($ugl['group_member'][$i]['user_nickname']));
                                    }
                                }else{
                                    if($page['login_flg'] && (int)$page['user_data']['user_id']===(int)$ugl['group_member'][$i]['user_id']){
                                        printf('、<span class="badge bg-lightblue">%s</span>'."\n", h($ugl['group_member'][$i]['user_nickname']));                                            
                                    }else{
                                        printf('、%s'."\n", h($ugl['group_member'][$i]['user_nickname']));
                                    }
                                }
                            }
                        }
                        printf('</p>'."\n");
                        printf('<div class="read-more">'."\n");
                        printf('<a href="/group/%s/">グループに移動</a>'."\n", h($ugl['group_key']));
                        printf('</div>'."\n");
                        printf('</div>'."\n");
                        printf('</div>'."\n");
                    }
                    printf('</div>'."\n");
                }else if($page['login_flg'] && $user_group_list===[]){
                    printf('参加しているグループがありません'."\n");
                }else{
                    printf('ログインされていません'."\n");
                    printf('<div class="btn-group">'."\n");
                    printf('<button class="btn btn-normal btn-lightblue" type="button" onclick="location.href=\'/login/\'">ログイン</button>'."\n");
                    printf('</div>'."\n");
                }
            ?>
            <div class="btn-group">
                <button class="btn btn-normal btn-red" type="button" onclick="location.href='/group/edit/'">グループを作成</button>
            </div>
            <div class="block block-white" style="margin-top:2rem;">
                <div class="block-body">
                    <div class="block-contents">
                        <p style="text-align:right;"><a href="/leave/"><?php echo SITE_NAME; ?>を退会する</a></p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
