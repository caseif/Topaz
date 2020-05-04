<?php
function render_pager(int $page, int $page_count) {
    $prev_page = $page - 1;
    $next_page = $page + 1;

    echo<<<HTML
    <div id="pager">
    HTML;

    if ($page !== 1) {
        echo <<<HTML
        <span class="pager-control">
            <a href="/?page=1">&laquo;</a>
        </span>
        <span class="pager-control">
            <a href="/?page={$prev_page}">&lsaquo;</a>
        </span>
        HTML;
    } else {
        echo <<<HTML
        <span class="pager-control disabled">
            &laquo;
        </span>
        <span class="pager-control disabled">
            &lsaquo;
        </span>
        HTML;
    }

    echo <<<HTML
    <span class="pager-label">
        Page {$page}/{$page_count}
    </span>
    HTML;

    if ($page !== $page_count) {
        echo <<<HTML
        <span class="pager-control">
            <a href="/?page={$next_page}">&rsaquo;</a>
        </span>
        <span class="pager-control">
            <a href="/?page={$page_count}">&raquo;</a>
        </span>
        HTML;
    } else {
        echo <<<HTML
        <span class="pager-control disabled">
            &rsaquo;
        </span>
        <span class="pager-control disabled">
            &raquo;
        </span>
        HTML;
    }

    echo<<<HTML
    </div>
    HTML;
}
?>
