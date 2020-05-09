<?php
require_once dirname(__FILE__).'/../../util/_global_config.php';
require_once dirname(__FILE__).'/../../util/_sec_config.php';

$_db_link = null;

function init_db(): void {
    global $_db_link;

    $_db_addr = get_secret_config()['database']['address'];
    $_db_name = get_secret_config()['database']['name'];
    $_db_user = get_secret_config()['database']['user'];
    $_db_pass = get_secret_config()['database']['pass'];

    $_db_link = mysqli_connect($_db_addr, $_db_user, $_db_pass);

    if ($_db_link->connect_errno) {
        throw new RuntimeException('MySQL connect failed: '.$_db_link->connect_error);
    }

    if (!get_db_link()->query("USE `{$_db_name}`")) {
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
