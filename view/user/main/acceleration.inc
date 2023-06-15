
<div class="wrapper">
    <div class="container">
        <main>
            <h1>加速度センサーのデモ</h1>
            <img src="/assets/img/acc_ios.jpg">
            <img src="/assets/img/acc_android.jpg">
            <div id="result"></div>
            <div id="ax"></div>
            <div id="ay"></div>
            <div id="az"></div>
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
