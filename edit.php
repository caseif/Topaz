<?php
require_once "./util/db/_db_posts.php";
require_once "./util/_page_config.php";
include_once "./template/_post.php";

$post = null;
if (isset($_GET['id'])) {
    $post = get_post($_GET['id'], GlobalConfig\SHOW_HIDDEN_POSTS, false);

    if ($post === null) {
        http_response_code(404);
        include('./error/404.php');
        die();
    }
}

if ($post !== null) {
    PageConfig::$title = 'Editing \''.$post->title.'\'';
} else {
    PageConfig::$title = 'Create Post';
}

include_once "./template/_header.php";
?>

<main id="main-content">
    <header class="header">
        <?php
        echo $post !== null ? "Edit Post" : "Create Post";
        ?>
    </header>
    <form id="edit-form">
        <?php
        if ($post !== null) {
            echo <<<HTML
                <input type="hidden" id="edit-id-input" name="id" value="{$post->id}" />
            HTML;
        }

        $title_val = $post !== null ? 'value="'.$post->title.'"' : '';
        $content_val = $post !== null ? $post->content : '';
        $about_sel = ($post !== null && $post->about) ? 'checked="checked"' : 'nope';
        
        echo <<<HTML
            <div id="edit-title" class="edit-section">
                <div class="edit-label">
                    <label for="edit-title-input">Title</label>
                </div>
                <input type="text" id="edit-title-input" name="title" {$title_val} />
            </div>
            <div id="edit-content" class="edit-section">
                <div class="edit-label">
                    <label for="edit-content-input">Content</label>
                </div>
                <textarea id="edit-content-input" name="content" resizeable="false">{$content_val}</textarea>
            </div>
            <div id="edit-about" class="edit-section">
                <input type="checkbox" id="edit-about-input" name="about" {$about_sel} />
                <label for="edit-about-input">Use as About page?</label>
            </div>

            <div class="edit-section">
                <button type="button" id="edit-submit" class="btn-primary btn-ajax">
                    Save
                    <span class="fas fa-circle-notch fa-spin"></span>
                </button>
            </div>
        HTML;
        ?>
    </form>
</main>

<?php
include_once "./template/_sidebar.php";
include_once "./template/_footer.php";
?>
