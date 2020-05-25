<?php
function redirect_back() {
    $redir_page = $_POST['back'] ?? '/';
    if (empty($redir_page)) {
        $redir_page = '/';
    }
    header("Location: {$redir_page}");
    die();
}

if ($_SESSION['user_id'] !== null) {
    unset($_SESSION['user_id']);
}

redirect_back();
?>
