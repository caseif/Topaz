<?php
function is_null_or_empty(string $str) {
    return $str == null || strlen($str) == 0;
}

function get_post_val(string $name) {
    return $_POST[$name] ?? '';
}

function redirect_back() {
    $redir_page = $_POST['back'] ?? '/';
    if (empty($redir_page)) {
        $redir_page = '/';
    }
    header("Location: {$redir_page}");
    die();
}
?>
