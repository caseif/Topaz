<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/model/_user.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/_password.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/_utility.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/db/_db_common.php';

function get_user(int $user_id): ?User {
    $query =   'SELECT * FROM `users` WHERE `id`=? LIMIT 1';

    $stmt = get_db_link()->prepare($query);

    if (!$stmt) {
        throw new RuntimeException('MySQL prepare failed: '.get_db_link()->error);
    }

    $stmt->bind_param('i', $user_id);

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

    return new User($row['id'], $row['username'], $row['name'], $row['register_time'], $row['active'] === 1,
            $row['permission_mask']);
}

function create_user(string $username, string $name, string $password, string $reg_code): int {
    $admin = false;

    if ($reg_code === GlobalConfig\get_config()->admin_ticket) {
        $admin = true;
    } else {
        //TODO: regular user registration codes?

        throw new UnexpectedValueException("The provided ticket is not valid");
    }

    $select_query = 'SELECT 1 FROM `users` WHERE `username`=?';
    $select_stmt = get_db_link()->prepare($select_query);
    if (!$select_stmt) {
        throw new RuntimeException('MySQL prepare failed: '.get_db_link()->error);
    }

    $username = trim($username);
    $select_stmt->bind_param('s', $username);

    if (!$select_stmt->execute()) {
        $select_stmt->close();
        throw new RuntimeException('MySQL execute failed: '.get_db_link()->error);
    }

    $res = $select_stmt->get_result();
    $select_stmt->close();

    try {
        if ($res->num_rows > 0) {
            throw new UnexpectedValueException('This username is already in use');
        }
    } finally {
        $res->close();
    }

    
    $insert_query = 'INSERT INTO `users` (`username`, `name`, `pass_hash`, `register_time`, `active`, `permission_mask`)
                         VALUES (?, ?, ?, ?, 1, ?);';
    
    $pass_hash = generate_hash($password);

    $insert_stmt = get_db_link()->prepare($insert_query);
    if (!$insert_stmt) {
        throw new RuntimeException('MySQL prepare failed: '.get_db_link()->error);
    }

    $username = trim($username);
    $name = trim($name);
    $time = time();
    $insert_stmt->bind_param('sssii', $username, $name, $pass_hash, $time, $admin ? 0xffffffff : 0);

    if (!$insert_stmt->execute()) {
        $insert_stmt->close();
        throw new RuntimeException('MySQL execute failed: '.get_db_link()->error);
    }

    $insert_stmt->close();

    return get_db_link()->insert_id;
}

function validate_login(string $username, string $plaintext_pass): ?int {
    $select_query = 'SELECT * FROM `users` WHERE `username`=?';
    $select_stmt = get_db_link()->prepare($select_query);
    if (!$select_stmt) {
        throw new RuntimeException('MySQL prepare failed: '.get_db_link()->error);
    }

    $username = trim($username);
    $select_stmt->bind_param('s', $username);

    if (!$select_stmt->execute()) {
        $select_stmt->close();
        throw new RuntimeException('MySQL execute failed: '.get_db_link()->error);
    }

    $res = $select_stmt->get_result();
    $select_stmt->close();

    if ($res->num_rows === 0) {
        $res->close();
        throw new UnexpectedValueException('The username/password combination was not recognized');
    }

    $row = $res->fetch_assoc();
    $db_hash = $row['pass_hash'];

    if (!verify_password($plaintext_pass, $db_hash)) {
        throw new UnexpectedValueException('The username/password combination was not recognized');
    } else if ($row['active'] === 0) {
        throw new UnexpectedValueException('This account is not active');
    }
    
    return $row['id'];
}
