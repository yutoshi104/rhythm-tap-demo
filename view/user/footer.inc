    <footer>
        <div class="container">
            <a class="footer-brand menu-link" href="/">
                Logo
            </a>
            <div class="sns" name="sns">
                <div class="sns-content bubble-btn twitter-animation"><div><a href="https://twitter.com/<?php echo h(TWITTER_USERNAME); ?>"><img src="/assets/src/sns/twitter.png" class="sns-icon" alt="Twitter"></a></div></div>
                <div class="sns-content bubble-btn github-animation"><div><a href="https://github.com/<?php echo h(GITHUB_ID); ?>"><img src="/assets/src/sns/github.png" class="sns-icon" alt="GitHub"></a></div></div>
            </div>
            <p class="copyright"><?php echo COPYRIGHT; ?></p>
        </div>
    </footer>

    <script src="/assets/js/common.js?v=<?php echo date("YmdHis"); ?>"></script>
    <?php
        if(isset($page['foot_js']) && !empty($page['foot_js'])){
            foreach($page['foot_js'] as $v){
                printf('<script src="%s"></script>'."\n", $v);
            }
        }
    ?>
</body>
</html>