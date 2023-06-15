
<div class="wrapper">
    <div class="container">
        <main>
            <h1>バイブレーションのデモ</h1>
            <p>ボタンを押すと2秒間振動します</p>
            <button id="btn">振動</button>
            <div id="result"></div>
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
