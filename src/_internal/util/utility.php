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

function get_array_item(array $arr, string $key, $def = null) {
    if (isset($arr[$key])) {
        return $arr[$key];
    } else {
        return $def;
    }
}

function get_session_var(string $key, $def = null) {
    return get_array_item($_SESSION, $key, $def);
}

function get_server_base_address() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
    $hostname = $_SERVER['SERVER_ADDR'];
    $port = $_SERVER['SERVER_PORT'];

    $full = $protocol.'://'.$hostname;
    if (!(($protocol == 'http' && $port == 80) || ($protocol == 'https' && $port == 443))) {
        $full .= ':'.$port;
    }

    return $full;
}

function to_server_url(string $path) {
    $url = get_server_base_address();
    if ($path[0] != '/') {
        $url .= '/';
    }
    $url .= $path;
    return $url;
}
?>
