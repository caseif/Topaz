<?php
abstract class NavbarLink {
    protected string $url;

    abstract function to_html(): string;
}

class TextNavbarLink extends NavbarLink {
    private string $text;

    function __construct(string $url, string $text) {
        $this->url = $url;
        $this->text = $text;
    }

    function to_html(): string {
        return <<<HTML
        <div class="navbar-link link-text">
            <a href="{$this->url}">{$this->text}</a>
        </div>
        HTML;
    }
}

class ImageNavbarLink {
    private string $fa_ns;
    private string $fa_icon;

    function __construct(string $url, string $fa_namespace, string $fa_icon) {
        $this->url = $url;
        $this->fa_ns = $fa_namespace;
        $this->fa_icon = $fa_icon;
    }

    function to_html(): string {
        return <<<HTML
        <div class="navbar-link link-icon">
            <a href="{$this->url}">
                <span class="{$this->fa_ns} fa-{$this->fa_icon}"></span>
            </a>
        </div>
        HTML;
    }
};
?>
