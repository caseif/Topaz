<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/_utility.php';

session_start();

if (isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
}

redirect_back();
?>
