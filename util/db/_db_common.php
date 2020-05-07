<?php
require_once dirname(__FILE__).'/../../util/_global_config.php';

$_db_link = null;

function init_db(): void {
    global $_db_link;

    $_sec_cfg_str = file_get_contents(GlobalConfig\SEC_CFG_FILE);
    $_sec_cfg_json = json_decode($_sec_cfg_str, true);
    if ($_sec_cfg_json === null) {
        throw new RuntimeException('Failed to load secret config');
    }

    $_db_user = $_sec_cfg_json['database']['user'];
    $_db_pass = $_sec_cfg_json['database']['pass'];

    $_db_link = mysqli_connect(GlobalConfig\DB_ADDR, $_db_user, $_db_pass);

    if ($_db_link->connect_errno) {
        throw new RuntimeException('MySQL connect failed: '.$_db_link->connect_error);
    }

    if (!get_db_link()->query('USE `'.GlobalConfig\DB_NAME.'`')) {
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
