
<div class="wrapper">
    <div class="container">
        <main>
            <h1>ジャイロセンサーのデモ</h1>
            <ul style="margin-bottom:1rem;">
                <li>alpha: デバイスが北から時計回りにどれだけ回転したかを表す角度（0～360）</li>
                <li>beta: デバイスが前後に傾いた角度（-180～180）</li>
                <li>gamma: デバイスが左右に傾いた角度（-90～90）</li>
            </ul>
            <div id="result"></div>
            <div id="alpha"></div>
            <div id="beta"></div>
            <div id="gamma"></div>
        </main>
        <?php 
            if(file_exists(VIEW_DIR."/user/sidebar.inc")){
                include VIEW_DIR."/user/sidebar.inc";
            }
        ?>
    </div>
    <?php 
        if(file_exists(VIEW_DIR."/user/footerbar.inc")){
            include VIEW_DIR."/user/footerbar.inc";
        }
    ?>
</div>
