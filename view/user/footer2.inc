
    <script src="/assets/js/common.js"></script>
    <?php
        if(isset($page['foot_js']) && !empty($page['foot_js'])){
            foreach($page['foot_js'] as $v){
                printf('<script src="%s"></script>'."\n", $v);
            }
        }
    ?>
</body>
</html>
