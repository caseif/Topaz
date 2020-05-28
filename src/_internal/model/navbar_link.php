<?php
abstract class NavbarLink {
    protected string $url;

    abstract function to_html(): string;
}

class TextNavbarLink extends NavbarLink {
    private string $text;
    private string $title_text;
    private string $new_tab;

    function __construct(string $url, string $text, ?string $title_text = null, bool $new_tab = false) {
        $this->url = $url;
        $this->text = $text;
        $this->title_text = $title_text ?? $text;
        $this->new_tab = $new_tab;
    }

    function to_html(): string {
        $title_attr = $this->title_text !== null ? "title=\"{$this->title_text}\"" : '';
        $target_attr = $this->new_tab ? 'target="_blank"' : '';

        return <<<HTML
        <div class="navbar-link link-text">
            <a href="{$this->url}" {$title_attr} {$target_attr}>
                {$this->text}
            </a>
        </div>
        HTML;
    }
}

class ImageNavbarLink {
    private string $fa_ns;
    private string $fa_icon;
    private ?string $title_text;
    private bool $new_tab;

    function __construct(string $url, string $fa_namespace, string $fa_icon, ?string $title_text = null,
            bool $new_tab = false) {
        $this->url = $url;
        $this->fa_ns = $fa_namespace;
        $this->fa_icon = $fa_icon;
        $this->title_text = $title_text;
        $this->new_tab = $new_tab;
    }

    function to_html(): string {
        $title_attr = $this->title_text !== null ? "title=\"{$this->title_text}\"" : '';
        $target_attr = $this->new_tab ? 'target="_blank"' : '';

        return <<<HTML
        <div class="navbar-link link-icon">
            <a href="{$this->url}" {$title_attr} {$target_attr}>
                <span class="{$this->fa_ns} fa-{$this->fa_icon}"></span>
            </a>
        </div>
        HTML;
    }
};
?>
