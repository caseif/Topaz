<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/global_config.php';

$_db_link = null;

function init_db(): void {
    global $_db_link;

    $db_config = GlobalConfig\get_config()->database;

    $_db_link = mysqli_connect($db_config->address, $db_config->user, $db_config->password);

    if ($_db_link->connect_errno) {
        throw new RuntimeException('MySQL connect failed: '.$_db_link->connect_error);
    }

    if (!get_db_link()->query("USE `{$db_config->name}`")) {
        throw new RuntimeException('MySQL change DB failed: '.get_db_link()->error);
    }
}

function get_db_link(): ?mysqli {
    global $_db_link;

    if ($_db_link === null) {
        init_db();
    }

    return $_db_link;
}
?>
