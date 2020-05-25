<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/_global_config.php';

$_sec_cfg_json = null;

function load_secret_config(): ?array {
    global $_sec_cfg_json;

    $_sec_cfg_str = file_get_contents(GlobalConfig\SEC_CFG_FILE);
    return json_decode($_sec_cfg_str, true);
}

function get_secret_config(): ?array {
    global $_sec_cfg_json;

    if ($_sec_cfg_json === null) {
        $_sec_cfg_json = load_secret_config();

        if ($_sec_cfg_json === null) {
            throw new RuntimeException('Failed to load secret config');
        }
    }

    return $_sec_cfg_json;
}
?>
