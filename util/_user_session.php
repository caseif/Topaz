<?php
require_once dirname(__FILE__).'/../util/db/_db_users.php';

session_start();

$_seen_user_id = null;

if (isset($_SESSION['user_id'])) {
    $_seen_user_id = $_SESSION['user_id'];
}

$cur_user = null;

if ($_seen_user_id !== null) {
    $cur_user = get_user($_seen_user_id);
}
?>