<?php
class PageConfig {
    public static string $title;

    function update(string $title) {
        PageConfig::$title = $title;
    }
};
?>
