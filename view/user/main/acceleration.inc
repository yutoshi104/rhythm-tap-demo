
<div class="wrapper">
    <div class="container">
        <main>
            <h1>加速度センサーのデモ</h1>
            <p id="result"></p>
            <p id="ax"></p>
            <p id="ay"></p>
            <p id="az"></p>
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
