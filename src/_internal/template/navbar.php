<div id="navbar">
    <div id="navbar-title">
        <a href="/"><?php echo GlobalConfig\get_config()->content->site_header; ?></a>
    </div>
    <div class="spacer"></div>
    <div id="navbar-links">
        <?php
        foreach (GlobalConfig\get_config()->content->navbar_links as $link) {
            echo $link->to_html();
        }
        ?>
    </div>
</div>
