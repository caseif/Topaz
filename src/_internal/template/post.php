<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/model/post.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/post_util.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/user_session.php';

function aware_substr(string $str, int $len) {
    if (strlen($str) <= $len) {
        return $str;
    }

    $new_len = $len;
    while ($new_len > 0 && $str[$new_len] !== ' ' && $str[$new_len] !== "\n") {
        $new_len--;
    }

    if ($new_len === 0) {
        $new_len = $len;
    }

    return substr($str, 0, $new_len);
}

function render_post(Post $post, bool $abridge = false): void {
    global $current_user;

    $human_date = date('F j, Y', $post->create_time);
    $robot_date = date('Y-m-d', $post->create_time);

    $full_post_link = "";

    $post_content = $post->content_parsed;

    if ($abridge) {
        $post_content = abridge_text($post_content, GlobalConfig\get_config()->display->post_preview_chars);
        $full_post_link = <<<HTML
        <div class="post-full-link">
            <a href="/post.php?id={$post->id}">See Full Post</a>
        </div>
        HTML;
    }

    $disp_title = $post->title;
    if (!$post->visible) {
        $disp_title .= ' [Hidden]';
    }

    if ($abridge) {
        $disp_title = "<a href=\"/post.php?id=$post->id\">$disp_title</a>";
    }

    $controls_html = '';
    if ($current_user !== null
            && ($current_user->permissions->write_other
                    || ($current_user->id === $post->author_id && $current_user->permissions->write))) {
        $hide_lbl = $post->visible ? 'Hide ' : 'Unhide';
        $hide_fn = $post->visible ? 'confirmHide' : 'confirmUnhide';

        $controls_html = <<<HTML
        <div class="post-controls">
            <div class="post-controls-section">
                <span class="post-control">
                    <a href="/edit.php?id={$post->id}">Edit</a>
                </span>
                <span class="post-control">
                    <a href="#" onclick="{$hide_fn}({$post->id});">{$hide_lbl}</a>
                </span>
            </div>
            <div class="post-controls-section">
                <span class="post-control">
                    <a href="#" onclick="confirmPermaDelete({$post->id});">Delete Permanently</a>
                </span>
            </div>
        </div>
        HTML;
    }

    $post_sig = '';
    if (!$post->about) {
        $post_sig = <<<HTML
        <div class="post-signature">
            Posted by {$post->author_name} on <time datetime="{$robot_date}">{$human_date}</time>
        </div>
        HTML;
    }

    echo <<<HTML
    <article class="post" data-id="{$post->id}" data-title="{$post->title}">
        <header class="post-header">
            <div class="post-title">
                {$disp_title}
            </div>
        </header>
        <div class="post-body">
            {$post_content}
        </div>
        <footer class="post-footer">
            {$full_post_link}
            {$post_sig}
            {$controls_html}
        </footer>
    </article>
    HTML;
}
?>
