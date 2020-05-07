        </div> <!-- /page-body -->
        <div id="page-footer">
            <div class="footer-section">
                <?php
                echo sprintf('&copy;&nbsp;%s&ndash;%s&nbsp;%s',
                        GlobalConfig\COPYRIGHT_START,
                        GlobalConfig\COPYRIGHT_END ?? date('Y'),
                        GlobalConfig\COPYRIGHT_NAME);
                ?>
                <span class="spacer"></span>
                <?php
                $email = GlobalConfig\CONTACT_EMAIL;
                $email_msg = GlobalConfig\CONTACT_EMAIL_MSG;
                echo <<<HTML
                <a href="mailto:{$email}">{$email_msg}</a>
                HTML;
                ?>
            </div>
            <div class="footer-section">
                <span class="fab fa-github"></span>
                &nbsp;
                <?php
                $url = GlobalConfig\GITHUB_URL;
                echo <<<HTML
                <a href="{$url}" target="_blank" rel="noopener noreferrer">Source on GitHub</a>
                HTML;
                ?>
            </div>
            <div class="footer-section">
                Design inspired by
                <a href="https://dinnerbone.com" target="_blank" rel="noopener noreferrer">Dinnerblog</a>
            </div>
        </div>
    </div> <!-- /wrapper -->
</body>
</html>

<?php
get_db_link()->close();
?>
