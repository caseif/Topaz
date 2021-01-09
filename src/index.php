<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/pager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/post.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/page_config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/password.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/db/db_posts.php';

PageConfig::$title = "Home";

$page = $_GET['page'] ?? 1;

if (!is_numeric($page)) {
    $page = 1;
}

$page_count = ceil(get_post_count() / GlobalConfig\get_config()->display->home_recent_post_count);

if ($page < 1 || ($page_count > 0 && $page > $page_count)) {
    http_response_code(404);
    include $_SERVER['DOCUMENT_ROOT'].'/error/404.php';
    die();
}

include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/header.php';
?>

<div id="main-content">
    <div id="post-previews">
        <?php
        $posts = get_posts(($page - 1) * GlobalConfig\get_config()->display->home_recent_post_count,
                GlobalConfig\get_config()->display->home_recent_post_count, true);

        if (count($posts) > 0) {
            foreach ($posts as $post_index => $post) {
                render_post($post, true);
            }
        } else {
            echo "There's nothing here...";
        }
        ?>
    </div>

    <?php
    if ($page_count > 0) {
        render_pager($page, $page_count);
    }
    ?>
</div>

<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/sidebar.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/footer.php';
?>
