<?php
require_once dirname(__FILE__).'/../util/_global_config.php';
require_once dirname(__FILE__).'/../model/_post.php';

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
        $row['id'],
        $row['title'],
        $row['content'],
        $row['create_time'],
        $row['update_time'],
        $row['author'],
        $row['author_name'] ?? null,
        $row['category'],
        $row['visible'],
        $row['about']
    );
}

function get_post_count($visible_only = true): int {
    if (!get_db_link()->query('USE `'.GlobalConfig\DB_NAME.'`')) {
        throw new RuntimeException('MySQL change db failed: '.get_db_link()->error);
    }

    $query = 'SELECT COUNT(*) FROM `posts`';

    if ($visible_only) {
        $query .= ' WHERE `visible`=1';
    }

    $res = get_db_link()->query($query);

    if (!$res) {
        throw new RuntimeException('MySQL query failed: '.get_db_link()->error);
    }
    
    $count = $res->fetch_row()[0];

    $res->close();

    return $count;
}

function get_posts($offset = -1, $limit = -1, $reverse = false, $ignore_visibility = false): array {
    if (!get_db_link()->query('USE `'.GlobalConfig\DB_NAME.'`')) {
        throw new RuntimeException('MySQL change db failed: '.get_db_link()->error);
    }

    $query = 'SELECT p.*, u.`display` AS `author_name` FROM `posts` p
              INNER JOIN `login` AS u ON p.`author` = u.`id`';

    if (!$ignore_visibility) {
        $query .= ' WHERE p.`visible`=1';
    }

    $query .= ' ORDER BY p.`create_time`';
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
        throw new RuntimeException('MySQL query failed: '.get_db_link()->error);
    }
    
    $posts = array();
    while ($row = $res->fetch_assoc()) {
        $posts[] = row_to_post($row);
    }

    $res->close();

    return $posts;
}

function get_post(int $id, bool $ignore_visibility = false): ?Post {
    if (!get_db_link()->query('USE `'.GlobalConfig\DB_NAME.'`')) {
        throw new RuntimeException('MySQL change db failed: '.get_db_link()->error);
    }

    $query =   'SELECT p.*, u.`display` AS `author_name` FROM `posts` p
                    INNER JOIN `login` AS u ON p.`author` = u.`id`
                    WHERE p.`id`=? AND p.`about`=0';
    if (!$ignore_visibility) {
        $query .= ' AND p.`visible`=1';
    }
    $query .= ' LIMIT 1';

    $stmt = get_db_link()->prepare($query);

    if (!$stmt) {
        throw new RuntimeException('MySQL prepare failed: '.get_db_link()->error);
    }

    $stmt->bind_param('i', $id);

    $stmt->execute();
    $res = $stmt->get_result();

    if (!$res) {
        throw new RuntimeException('MySQL query failed: '.get_db_link()->error);
    }

    if ($res->num_rows === 0) {
        return null;
    }
    
    $row = $res->fetch_assoc();

    $res->close();
    $stmt->close();

    return row_to_post($row);
}

function get_about(): ?Post {
    if (!get_db_link()->query('USE `'.GlobalConfig\DB_NAME.'`')) {
        throw new RuntimeException('MySQL change db failed: '.get_db_link()->error);
    }

    $query =   'SELECT p.*, u.`display` AS `author_name` FROM `posts` p
                    INNER JOIN `login` AS u ON p.`author` = u.`id`
                    WHERE p.`id`=? AND p.`visible`=1 LIMIT 1';
    $stmt = get_db_link()->prepare($query);

    if (!$stmt) {
        throw new RuntimeException('MySQL prepare failed: '.get_db_link()->error);
    }

    $stmt->bind_param('i', $id);

    $stmt->execute();
    $res = $stmt->get_result();

    if (!$res) {
        throw new RuntimeException('MySQL query failed: '.get_db_link()->error);
    }

    if ($res->num_rows === 0) {
        return null;
    }
    
    $row = $res->fetch_assoc();

    $res->close();
    $stmt->close();

    return row_to_post($row);
}

function create_post(string $title, string $content, bool $about): int {
    if (!get_db_link()->query('USE `'.GlobalConfig\DB_NAME.'`')) {
        throw new RuntimeException('MySQL change db failed: '.get_db_link()->error);
    }

    $query = 'INSERT INTO `posts` (`title`, `content`, `about`, `visible`, `create_time`) VALUES (?, ?, ?, 1, ?);';
    if ($about) {
        $query = 'UPDATE `posts` SET `about`=0;'.$query;
    }

    $stmt = get_db_link()->prepare($query);

    if (!$stmt) {
        throw new RuntimeException('MySQL prepare failed: '.get_db_link()->error);
    }

    $about_i = intval($about);
    $time = time();
    $stmt->bind_param('ssii', $title, $content, $about_i, $time);

    if (!$stmt->execute()) {
        $stmt->close();
        throw new RuntimeException('MySQL execute failed: '.get_db_link()->error);
    }

    $stmt->close();

    return get_db_link()->insert_id;
}

function edit_post(int $id, string $title, string $content, bool $about): bool {
    if (!get_db_link()->query('USE `'.GlobalConfig\DB_NAME.'`')) {
        throw new RuntimeException('MySQL change db failed: '.get_db_link()->error);
    }

    $query = 'UPDATE `posts` SET `title`=?, `content`=?, `about`=?, `update_time`=? WHERE `id`=?';

    $stmt = get_db_link()->prepare($query);

    if (!$stmt) {
        throw new RuntimeException('MySQL prepare failed: '.get_db_link()->error);
    }

    $about_i = intval($about);
    $time = time();
    $stmt->bind_param('ssiii', $title, $content, $about_i, $time, $id);

    $res = $stmt->execute();
    $stmt->close();
    
    return $res;
}

function update_post_visibility(int $id, bool $visible): bool {
    $query = 'UPDATE `posts` SET `visible`=? WHERE `id`=?';

    $stmt = get_db_link()->prepare($query);

    if (!$stmt) {
        throw new RuntimeException('MySQL prepare failed: '.get_db_link()->error);
    }

    $visible_i = intval($visible);
    $stmt->bind_param('ii', $visible_i, $id);

    $res = $stmt->execute();
    $stmt->close();
    
    return $res;
}

function delete_post(int $id): bool {
    $query = 'DELETE FROM `posts` WHERE `id`=?';

    $stmt = get_db_link()->prepare($query);

    if (!$stmt) {
        throw new RuntimeException('MySQL prepare failed: '.get_db_link()->error);
    }

    $stmt->bind_param('i', $id);

    $res = $stmt->execute();
    $stmt->close();
    
    return $res;
}
?>
