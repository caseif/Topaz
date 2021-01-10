<?php
function abridge_text(string $text, int $len) {
    $dom = new DOMDocument();
    if (!$dom->loadHTML('<?xml encoding="utf-8" ?>'.$text)) {
        throw new RuntimeException("Failed to parse DOM from post body");
    }

    $root_node = $dom->getElementsByTagName('body')[0];

    $new_dom = new DOMDocument();
    $avail_chars = $len;

    $new_root = abridge_node($new_dom, $root_node, $avail_chars);
    $new_root = $new_dom->importNode($new_root);
    $new_dom->appendChild($new_root);

    $res = "";
    foreach ($new_root->childNodes as $child) {
        $res .= $new_dom->saveHTML($child);
    }

    return $res;
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
?>
