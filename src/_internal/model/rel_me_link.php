<?php
class RelMeLink {
    private string $url;

    function __construct(string $url) {
        $this->url = $url;
    }

    function to_html(): string {
        return <<<HTML
        <link rel="me" href="{$this->url}" />
        HTML;
    }
}
?>
