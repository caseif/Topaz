<?php
function isNullOrEmpty(string $str) {
    return $str == null || strlen($str) == 0;
}

function getPostVal(string $name) {
    return $_POST[$name] ?? '';
}
?>
