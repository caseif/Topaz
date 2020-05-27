<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/db/_db_common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/model/_post.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/_user_session.php';

function row_to_post(array $row): Post {
    return new Post(
        $row['id'],
        $row['title'],
        $row['content'],
        $row['create_time'],
        $row['update_time'],
        $row['author'],
        $row['author_name'],
        $row['category'],
        $row['category_name'],
        $row['visible'],
        $row['about']
    );
}

function get_post_count(bool $ignore_visibility = false): int {
    global $current_user;

    $bind_user_param = false;

    $query = 'SELECT COUNT(*) FROM `posts` p WHERE p.`about`=0';

    if (!$ignore_visibility) {
        $query .= ' AND (p.`visible`=1';
        
        if ($current_user !== null) {
            $query .= ' OR p.`author`=?';
            $bind_user_param = true;
        }
        
        $query .= ')';
    }

    $stmt = get_db_link()->prepare($query);

    if (!$stmt) {
        throw new RuntimeException('MySQL prepare failed: '.get_db_link()->error);
    }

    if ($bind_user_param) {
        $stmt->bind_param('i', $current_user->id);
    }

    if (!$stmt->execute()) {
        throw new RuntimeException('MySQL execute failed: '.get_db_link()->error);
    }

    $res = $stmt->get_result();

    if (!$res) {
        throw new RuntimeException('MySQL query failed: '.get_db_link()->error);
    }
    
    $count = $res->fetch_row()[0];

    $res->close();

    return $count;
}

function get_posts(int $offset = -1, int $limit = -1, bool $reverse = false): array {
    global $current_user;

    $bind_user_param = false;

    $query = 'SELECT p.*,
                     IFNULL(u.`name`,\'Unknown\') AS `author_name`,
                     IFNULL(c.`display`,\'Uncategorized\') AS `category_name`
                FROM `posts` p
                LEFT JOIN `users` AS u ON p.`author`=u.`id`
                LEFT JOIN `categories` AS c ON p.`category`=c.`id`
                WHERE p.`about`=0';

    $query .= ' AND (p.`visible`=1';
    
    if ($current_user !== null) {
        $query .= ' OR p.`author`=?';
        $bind_user_param = true;
    }
    
    $query .= ')';

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

    $stmt = get_db_link()->prepare($query);

    if (!$stmt) {
        throw new RuntimeException('MySQL prepare failed: '.get_db_link()->error);
    }
    
    if ($bind_user_param) {
        $stmt->bind_param('i', $current_user->id);
    }

    if (!$stmt->execute()) {
        throw new RuntimeException('MySQL execute failed: '.get_db_link()->error);
    }

    $res = $stmt->get_result();

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

function get_post(int $id, bool $ignore_access = false, bool $exclude_about = true): ?Post {
    global $current_user;

    $bind_user_param = false;

    $query = 'SELECT p.*,
                     IFNULL(u.`name`,\'Unknown\') AS `author_name`,
                     IFNULL(c.`display`,\'Uncategorized\') AS `category_name`
                FROM `posts` p
                LEFT JOIN `users` AS u ON p.`author`=u.`id`
                LEFT JOIN `categories` AS c ON p.`category`=c.`id`
                WHERE p.`id`=?';
    
    if (!$ignore_access) {
        $query .= ' AND (p.`visible`=1';

        if ($current_user !== null) {
            $query .= ' OR p.`author`=?';
            $bind_user_param = true;
        }
    }

    $query .= ')';

    if ($exclude_about) {
        $query .= ' AND p.`about`=0';
    }
    $query .= ' LIMIT 1';

    $stmt = get_db_link()->prepare($query);

    if (!$stmt) {
        throw new RuntimeException('MySQL prepare failed: '.get_db_link()->error);
    }

    if ($bind_user_param) {
        $stmt->bind_param('ii', $id, $current_user->id);
    } else {
        $stmt->bind_param('i', $id);
    }

    if (!$stmt->execute()) {
        throw new RuntimeException('MySQL execute failed: '.get_db_link()->error);
    }

    $res = $stmt->get_result();
    $stmt->close();

    if (!$res) {
        throw new RuntimeException('MySQL query failed: '.get_db_link()->error);
    }

    if ($res->num_rows === 0) {
        return null;
    }
    
    $row = $res->fetch_assoc();

    $res->close();

    return row_to_post($row);
}

function get_about(): ?Post {
    global $current_user;
    
    $bind_user_param = false;

    $query =   'SELECT p.*, IFNULL(u.`name`, \'Unknown\') AS `author_name` FROM `posts` p
                    INNER JOIN `users` AS u ON p.`author` = u.`id`
                    WHERE p.`about`=1';
    
    $query .= ' AND (p.`visible`=1';
    if ($current_user !== null) {
        $query .= ' OR p.`author`=?';
        $bind_user_param = true;
    }
    $query .= ')';

    $query .= ' LIMIT 1';
    $stmt = get_db_link()->prepare($query);

    if (!$stmt) {
        throw new RuntimeException('MySQL prepare failed: '.get_db_link()->error);
    }

    if ($bind_user_param) {
        $stmt->bind_param('i', $current_user->id);
    }

    if (!$stmt->execute()) {
        $stmt->close();
        throw new RuntimeException('MySQL execute failed: '.get_db_link()->error);
    }

    $res = $stmt->get_result();
    $stmt->close();

    if (!$res) {
        throw new RuntimeException('MySQL query failed: '.get_db_link()->error);
    }

    if ($res->num_rows === 0) {
        return null;
    }
    
    $row = $res->fetch_assoc();

    $res->close();

    return row_to_post($row);
}

function create_post(string $title, string $content, bool $about): int {
    $insert_query = 'INSERT INTO `posts` (`title`, `content`, `about`, `author`, `create_time`, `visible`) VALUES (?, ?, ?, ?, ?, 1);';
    if ($about) {
        $rm_about_query = 'UPDATE `posts` SET `about`=0;';
        $rm_about_stmt = get_db_link()->prepare($rm_about_query);

        if (!$rm_about_stmt) {
            throw new RuntimeException('MySQL prepare failed: '.get_db_link()->error);
        }
    }

    $insert_stmt = get_db_link()->prepare($insert_query);

    if (!$insert_stmt) {
        throw new RuntimeException('MySQL prepare failed: '.get_db_link()->error);
    }

    $about_i = intval($about);
    $time = time();
    $user_id = 1; //TODO: temporary
    $insert_stmt->bind_param('ssisi', $title, $content, $about_i, $user_id, $time);

    if ($about) {
        if (!$rm_about_stmt->execute()) {
            $rm_about_stmt->close();
            throw new RuntimeException('MySQL execute failed: '.get_db_link()->error);
        }
    }

    if (!$insert_stmt->execute()) {
        $insert_stmt->close();
        throw new RuntimeException('MySQL execute failed: '.get_db_link()->error);
    }

    $insert_stmt->close();

    return get_db_link()->insert_id;
}

function edit_post(int $id, string $title, string $content, bool $about): bool {
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
