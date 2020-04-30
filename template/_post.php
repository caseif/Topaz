<?php
include_once "./lib/_parsedown.php";

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
            $text = aware_substr($text, $avail_chars).'...';
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
    $avail_chars = GlobalConfig\POST_ABRIDGED_CHARS;
    
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
    $parsedown = new Parsedown();

    $human_date = date('F j, Y', $post->time);
    $robot_date = date('Y-m-d', $post->time);

    $parsed_text = $parsedown->text($post->content);
    if ($abridge) {
        $parsed_text = abridge_text($parsed_text);
    }

    echo <<<HTML
    <article class="post">
        <header class="post-header">
            <div class="post-title">
                {$post->title}
            </div>
        </header>
        <div class="post-body">
            {$parsed_text}
        </div>
        <footer class="post-footer">
            Posted by {$post->author_name} on <time datetime="{$robot_date}">{$human_date}</time>
        </footer>
    </article>
    HTML;
}
?>
