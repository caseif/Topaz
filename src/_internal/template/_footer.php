        </div> <!-- /page-body -->
        <div id="page-footer">
            <div class="footer-section">
                <?php
                echo sprintf('&copy;&nbsp;%s&ndash;%s&nbsp;%s',
                        GlobalConfig\get_config()->copyright_start_year,
                        GlobalConfig\get_config()->copyright_end_year ?? date('Y'),
                        GlobalConfig\get_config()->copyright_name);
                ?>
                <span class="spacer"></span>
                <?php
                $email = GlobalConfig\get_config()->contact_email;
                $email_msg = GlobalConfig\get_config()->contact_email_text;
                echo <<<HTML
                <a href="mailto:{$email}">{$email_msg}</a>
                HTML;
                ?>
            </div>
            <div class="footer-section">
                <span class="fab fa-github"></span>
                &nbsp;
                <?php
                $url = GlobalConfig\get_config()->github_url;
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
