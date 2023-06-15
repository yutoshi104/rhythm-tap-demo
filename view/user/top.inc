
<div class="wrapper">
    <div class="container">
        <main>
            <h1><?php echo SITE_NAME; ?></h1>
            <p>main</p>
            <ul>
                <li><a href="/acceleration">加速度センサーのデモ</a></li>
                <li><a href="/gyro">ジャイロセンサーのデモ</a></li>
                <li><a href="/magnetism">磁気センサーのデモ</a></li>
                <li><a href="/vibration">バイブレーションのデモ</a></li>
                <li><a href="/audio_output">💮音声出力のデモ</a></li>
            </ul>
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
