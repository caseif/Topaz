<?php
//TODO: check permission

require_once('../util/db/_db_posts.php');

function action_save_post(?int $id, string $title, string $content, bool $about): array {
    try {
        if ($id !== null) {
            // update an existing post
            $res = edit_post($id, $title, $content, $about);

            if ($res) {
                return array(
                    'success' => true
                );
            } else {
                return array(
                    'success' => false,
                    'message' => 'Unknown error'
                );
            }
        } else {
            $new_id = create_post($title, $content, $about);

            return array(
                'success' => true,
                'id' => $new_id
            );
        }
    } catch (Exception $ex) {
        return array(
            'success' => false,
            'message' => $ex->getMessage()
        );
    }
}

function action_delete_post(int $id, bool $permanent) {
    try {
        if ($permanent) {
            $res = delete_post($id);
        } else {
            $res = update_post_visibility($id, false);
        }

        if ($res) {
            return array(
                'success' => true
            );
        } else {
            return array(
                'success' => false,
                'message' => 'Unknown error'
            );
        }
    } catch (Exception $ex) {
        return array(
            'success' => false,
            'message' => $ex->getMessage()
        );
    }
}

ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // create
    $put_data = array();
    parse_str(file_get_contents('php://input'), $put_data);
    $res = action_save_post(null, $put_data['title'], $put_data['content'], $put_data['about']);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // update
    $res = action_save_post($_POST['id'], $_POST['title'], $_POST['content'], $_POST['about']);
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // delete
    $delete_data = array();
    parse_str(file_get_contents('php://input'), $delete_data);
    $res = action_delete_post($delete_data['id'], $delete_data['perma'] === "true");
} else {
    http_response_code(405);
    die;
}

ob_end_clean();

header('Content-Type: application/json');
echo json_encode($res);
?>
