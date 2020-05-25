<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/db/_db_users.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $current_user = get_user($_SESSION['user_id']);
} else {
    $current_user = null;
}
?>
