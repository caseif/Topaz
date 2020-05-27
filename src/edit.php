<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/db/_db_posts.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/_page_config.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/_post.php';

$_created_id = null;

$form_error = null;

function handle_action(): ?string {
    global $_created_id;

    if (!isset($_POST['title']) || !isset($_POST['content'])) {
        return 'All required fields must be supplied';
    }

    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $about = ($_POST['about'] ?? '0') === '1';

    if (empty($title)) {
        return 'Title must not be empty';
    } else if (strlen($title) > 128) {
        return 'Title must not exceed 128 characters';
    } else if (empty($content)) {
        return 'Post body must not be empty';
    } else if (strlen($content) > 16777215) {
        return 'Post body must not exceed 16,777,215 characters';
    }

    try {
        if ($id !== null) {
            // update an existing post
            $res = edit_post($id, $title, $content, $about);

            if ($res) {
                return null;
            } else {
                return 'Unknown error';
            }
        } else {
            $_created_id = create_post($title, $content, $about);

             return null;
        }
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_error = handle_action();

    if ($form_error === null) {
        $id = $_created_id ?? $_POST['id'];
        if ($_POST['about'] === '1') {
            header('Location: /about.php');
        } else {
            header("Location: /post.php?id={$id}");
        }
        die();
    }
}

$post = null;
if (isset($_GET['id'])) {
    $post = get_post($_GET['id'], false, false);

    if ($post === null) {
        http_response_code(404);
        include $_SERVER['DOCUMENT_ROOT'].'/error/404.php';
        die();
    }

    if (!$current_user->admin && $post->author_id !== $current_user->id) {
        http_response_code(403);
        include $_SERVER['DOCUMENT_ROOT'].'/error/403.php';
        die();
    }
}

if ($post !== null) {
    PageConfig::$title = 'Editing \''.$post->title.'\'';
} else {
    PageConfig::$title = 'Create Post';
}

include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/_header.php';
?>

<main id="main-content">
    <header class="header">
        <?php
        echo $post !== null ? "Edit Post" : "Create Post";
        ?>
    </header>
    <form id="edit-form" method="POST">
        <input type="hidden" id="edit-action-input"></input>
        <?php
        if ($post !== null) {
            echo <<<HTML
                <input type="hidden" id="edit-id-input" name="id" value="{$post->id}" />
            HTML;
        }

        $title_val = get_post_val("title");
        if (empty($title_val)) {
            $title_val = $post !== null ? $post->title : '';
        }
        $content_val = get_post_val("content");
        if (empty($content_val)) {
            $content_val = $post !== null ? $post->content : '';
        }
        $about_str = get_post_val("content");
        $about_sel = false;
        if (!empty($about_str)) {
            $about_sel = $about_str === '1';
        } else {
            $about_sel = ($post !== null && $post->about);
        }
        $about_val = $about_sel ? 'checked="checked"' : '';
        
        echo <<<HTML
            <div class="form-section form-error">
                {$form_error}
            </div>
            <div id="edit-title" class="form-section">
                <label class="form-label required" for="edit-title-input">Title</label>
                <input type="text" id="edit-title-input" name="title" value="{$title_val}" required="required"
                        maxlength="128" />
            </div>
            <div id="edit-content" class="form-section">
                <label class="form-label required" for="edit-content-input">Content</label>
                <textarea id="edit-content-input" name="content" resizeable="false" required="required"
                        maxlength="16777215">{$content_val}</textarea>
            </div>
            <div id="edit-about" class="form-section">
                <input type="checkbox" id="edit-about-input" name="about" {$about_val} />
                <label for="edit-about-input">Use as About page?</label>
            </div>

            <div class="form-section">
                <button type="submit" id="edit-submit" class="btn-primary">
                    Save
                </button>
            </div>
        HTML;
        ?>
    </form>
</main>

<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/_sidebar.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/_footer.php';
?>
