<?php
class ExternalLink {
    private string $url;
    private string $text;
    private string $title_text;
    private bool $new_tab;

    function __construct(string $url, string $text, ?string $title_text = null, bool $new_tab = false) {
        $this->url = $url;
        $this->text = $text;
        $this->title_text = $title_text ?? $text;
        $this->new_tab = $new_tab;
    }

    function to_html() {
        $target_attr = $this->new_tab ? 'target="_blank"' : '';
        return <<<HTML
            <div class="sidebar-link">
                <a href="{$this->url}" title="{$this->title_text}" {$target_attr}>{$this->text}</a>
            </div>
        HTML;
    }
}
?>
