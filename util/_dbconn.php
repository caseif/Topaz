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
        $row["author_name"] ?? null,
        $row["category"],
        $row["visible"],
    );
}

function get_post_count($visible_only = true): int {
    if (!get_db_link()->query('USE `'.GlobalConfig\DB_NAME.'`')) {
        throw new RuntimeException("MySQL change db failed: ".get_db_link()->error);
    }

    $query = 'SELECT COUNT(*) FROM `posts`';

    if ($visible_only) {
        $query .= ' WHERE `visible`=1';
    }

    $res = get_db_link()->query($query);

    if (!$res) {
        throw new RuntimeException("MySQL query failed: ".get_db_link()->error);
    }
    
    $count = $res->fetch_row()[0];

    $res->close();

    return $count;
}

function get_posts($offset = -1, $limit = -1, $reverse = false, $visible_only = true): array {
    if (!get_db_link()->query('USE `'.GlobalConfig\DB_NAME.'`')) {
        throw new RuntimeException("MySQL change db failed: ".get_db_link()->error);
    }

    $query = 'SELECT p.*, u.`display` AS `author_name` FROM `posts` p
              INNER JOIN `login` AS u ON p.`author` = u.`id`';

    if ($visible_only) {
        $query .= ' WHERE `visible`=1';
    }

    $query .= ' ORDER BY `time`';
    if ($reverse) {
        $query .= ' DESC';
    }

    if ($limit > 0) {
        $query .= ' LIMIT '.$limit;
    }
    if ($offset >= 0) {
        $query .= ' OFFSET '.$offset;
    }

    $res = get_db_link()->query($query);

    if (!$res) {
        throw new RuntimeException("MySQL query failed: ".get_db_link()->error);
    }
    
    $posts = array();
    while ($row = $res->fetch_assoc()) {
        $posts[] = row_to_post($row);
    }

    $res->close();

    return $posts;
}

function get_post(int $id): ?Post {
    if (!get_db_link()->query('USE `'.GlobalConfig\DB_NAME.'`')) {
        throw new RuntimeException("MySQL change db failed: ".get_db_link()->error);
    }

    $stmt = get_db_link()->prepare('SELECT p.*, u.`display` AS `author_name` FROM `posts` p
                                    INNER JOIN `login` AS u ON p.`author` = u.`id`
                                    WHERE p.`id`=? AND `visible`=1 LIMIT 1');

    if (!$stmt) {
        throw new RuntimeException("MySQL prepare failed: ".get_db_link()->error);
    }

    $stmt->bind_param('i', $id);

    $stmt->execute();
    $res = $stmt->get_result();

    if (!$res) {
        throw new RuntimeException("MySQL query failed: ".get_db_link()->error);
    }

    if ($res->num_rows === 0) {
        return null;
    }
    
    $row = $res->fetch_assoc();

    $res->close();
    $stmt->close();

    return row_to_post($row);
}
?>
