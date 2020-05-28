<?php
//TODO: check permission

require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/db/db_posts.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die();
}

if (!isset($_POST['action']) || !isset($_POST['id'])) {
    http_response_code(400);
    die();
}

$action = $_POST['action'];
$id = $_POST['id'];

$post = get_post($id, false, false);

if ($post === null) {
    http_response_code(404);
    die();
}

if (!$current_user->permissions->write_other && $post->author_id !== $current_user->id) {
    http_response_code(403);
    die();
}

ob_start();

// update
$result = null;
$resp = null;
try {
    if ($action === 'hide') {
        $result = update_post_visibility($id, false);
    } else if ($action === 'unhide') {
        $result = update_post_visibility($id, true);
    } else if ($action === 'delete') {
        $result = delete_post($id);
    } else {
        http_response_code(400);
        die();
    }

    if (!$result) {
        $resp = array(
            'success' => false,
            'message' => 'Unknown error'
        );
    }

    $resp = array(
        'success' => true
    );
} catch (Exception $ex) {
    $resp = array(
        'success' => false,
        'message' => $ex->getMessage()
    );
}

ob_end_clean();

header('Content-Type: application/json');
echo json_encode($resp);
?>
