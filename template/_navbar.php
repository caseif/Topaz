<div id="navbar">
    <div id="navbar-title">
        <a href="/"><?php echo GlobalConfig\SITE_HEADER; ?></a>
    </div>
    <div class="spacer"></div>
    <div id="navbar-links">
        <?php
        foreach ($NAVBAR_LINKS as $link) {
            echo $link->to_html();
        }
        ?>
    </div>
</div>
