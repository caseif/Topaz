        </div> <!-- /page-body -->
        <div id="page-footer">
            <div class="footer-section">
                <?php
                $start_year = GlobalConfig\get_config()->content->copyright_start_year ?? date('Y');
                $end_year = GlobalConfig\get_config()->content->copyright_end_year ?? date('Y');
                if ($start_year == $end_year) {
                    echo sprintf('&copy;&nbsp;%s&nbsp;%s',
                            $start_year,
                            GlobalConfig\get_config()->content->copyright_name);
                } else {
                    echo sprintf('&copy;&nbsp;%s&ndash;%s&nbsp;%s',
                            $start_year,
                            $end_year,
                            GlobalConfig\get_config()->content->copyright_name);
                }
                ?>
                <span class="spacer"></span>
                <?php
                $email = GlobalConfig\get_config()->content->contact_email;
                $email_msg = GlobalConfig\get_config()->content->contact_email_text;
                echo <<<HTML
                <a href="mailto:{$email}">{$email_msg}</a>
                HTML;
                ?>
            </div>
            <div class="footer-section">
                Powered by Topaz
                <span class="spacer"></span>
                <span class="fab fa-github"></span>
                &nbsp;
                <?php
                $url = GlobalConfig\get_config()->content->github_url;
                echo <<<HTML
                <a href="{$url}" target="_blank" rel="noopener noreferrer">Source on GitHub</a>
                HTML;
                ?>
            </div>
        </div>
    </div> <!-- /wrapper -->
</body>
</html>

<?php
get_db_link()->close();
?>
