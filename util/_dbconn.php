<?php
include_once dirname(__FILE__)."/../util/_global_config.php";
include_once dirname(__FILE__)."/../model/_post.php";

$_db_link = null;

function init_db(): void {
    global $_db_link;

    $_db_cred_str = file_get_contents(GlobalConfig\DB_CRED_FILE);
    $_db_cred_json = json_decode($_db_cred_str, true);
    if ($_db_cred_json === null) {
        throw new RuntimeException("Failed to create MySQL connection");
    }

    $_db_user = $_db_cred_json["user"];
    $_db_pass = $_db_cred_json["pass"];

    $_db_link = mysqli_connect(GlobalConfig\DB_ADDR, $_db_user, $_db_pass);

    if ($_db_link->connect_errno) {
        throw new RuntimeException("MySQL connect failed: ".$_db_link->connect_error);
    }
}

function get_db_link(): ?mysqli {
    global $_db_link;

    if ($_db_link === null) {
        init_db();
    }

    return $_db_link;
}

function row_to_post(array $row): Post {
    return new Post(
        $row["id"],
        $row["title"],
        $row["content"],
        $row["time"],
        $row["author"],
        $row["category"],
        $row["visible"],
    );
}

function get_posts($offset = -1, $limit = -1, $reverse = false, $visible_only = true): array {
    if (!get_db_link()->query("USE `".GlobalConfig\DB_NAME."`")) {
        throw new RuntimeException("MySQL change db failed: ".get_db_link()->error);
    }

    $query = "SELECT * FROM `posts`";

    if ($visible_only) {
        $query .= " WHERE `visible`=1";
    }

    if ($limit > 0) {
        $query .= " LIMIT ".$limit;
    }
    if ($offset >= 0) {
        $query .= " OFFSET".$offset;
    }

    $query .= " ORDER BY `time`";
    if ($reverse) {
        $query .= " DESC";
    }

    $res = get_db_link()->query($query);

    if (!$res) {
        throw new RuntimeException("MySQL query failed: ".get_db_link()->error);
    }
    
    $posts = array();
    while ($row = $res->fetch_assoc()) {
        $posts[] = row_to_post($row);
    }
    return $posts;
}
?>
