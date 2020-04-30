<?php
class ExternalLink {
    private string $url;
    private string $text;
    private bool $new_tab;

    function __construct($url, $text, $new_tab = true) {
        $this->url = $url;
        $this->text = $text;
        $this->new_tab = $new_tab;
    }

    function to_html() {
        $target_attr = $this->new_tab ? 'target="_blank"' : '';
        return <<<HTML
            <div class="sidebar-link">
                <a href="{$this->url}" {$target_attr}>{$this->text}</a>
            </div>
        HTML;
    }
}
?>
