<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/lib/_parsedown.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/_user_session.php';

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

function abridge_node(DOMDocument $root, DOMNode $node, int &$avail_chars): DOMNode {
    if ($node instanceof DOMText) {
        $text = $node->textContent;
        
        if (strlen($node->textContent) >= $avail_chars) {
            $text = aware_substr($text, $avail_chars)."\u{2026}";
            $avail_chars = 0;
        } else {
            $avail_chars -= strlen($text);
        }
        return new DOMText($text);
    } else {
        $new_node = $root->importNode($node->cloneNode());

        foreach ($node->childNodes as $child) {
            $child_abridged = abridge_node($root, $child, $avail_chars);
            
            // cut off text before header
            if ($avail_chars === 0
                    && $child_abridged instanceof DOMElement
                    && preg_match("/h[1-6]/", $child_abridged->tagName)) {
                $new_node->appendChild(new DOMElement('p', '...'));
            } else {
                $new_node->appendChild($root->importNode($child_abridged));
            }

            if ($avail_chars === 0) {
                break;
            }
        }

        return $new_node;
    }
}

function abridge_text(string $text) {
    $dom = new DOMDocument();
    if (!$dom->loadHTML($text)) {
        throw new RuntimeException("Failed to parse DOM from post body");
    }
    
    $root_node = $dom->getElementsByTagName('body')[0];

    $new_dom = new DOMDocument();
    $avail_chars = GlobalConfig\get_config()->post_preview_chars;
    
    $new_root = abridge_node($new_dom, $root_node, $avail_chars);
    $new_root = $new_dom->importNode($new_root);
    $new_dom->appendChild($new_root);

    $res = $new_dom->saveHTML();
    if (!$res) {
        throw new RuntimeException("Failed to generate abridged DOM from post body");
    }
    
    return $res;
}

function render_post(Post $post, bool $abridge = false): void {
    global $current_user;

    $parsedown = new Parsedown();

    $human_date = date('F j, Y', $post->create_time);
    $robot_date = date('Y-m-d', $post->create_time);

    $full_post_link = "";
    $parsed_text = $parsedown->text($post->content);
    if ($abridge) {
        $parsed_text = abridge_text($parsed_text);
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

    echo <<<HTML
    <article class="post" data-id="{$post->id}" data-title="{$post->title}">
        <header class="post-header">
            <div class="post-title">
                {$disp_title}
            </div>
        </header>
        <div class="post-body">
            {$parsed_text}
        </div>
        <footer class="post-footer">
            {$full_post_link}
            <div class="post-signature">
                Posted by {$post->author_name} on <time datetime="{$robot_date}">{$human_date}</time>
            </div>
            {$controls_html}
        </footer>
    </article>
    HTML;
}
?>
