<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die;
}

//TODO: check permission

require_once('../util/_dbconn.php');

function do_save_action(): array {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $title = $_POST['title'];
    $content = $_POST['content'];
    $about = $_POST['about'];

    try {
        if (isset($id)) {
            // update an existing post
            $res = edit_post($id, $title, $content, $about);

            if ($res) {
                return array(
                    'success' => true
                );
            } else {
                return array(
                    'success' => false,
                    'message' => "Unknown error"
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

ob_start();
$res = do_save_action();
ob_end_clean();

header('Content-Type: application/json');
echo json_encode($res);
?>
