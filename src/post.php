<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/db/db_posts.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/page_config.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/post.php';

$post = get_post($_GET['id']);

if ($post === null) {
    http_response_code(404);
    include $_SERVER['DOCUMENT_ROOT'].'/error/404.php';
    die();
}

PageConfig::$title = $post->title;

include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/header.php';
?>

<main id="main-content">
    <?php
    render_post($post);
    ?>
</main>

<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/sidebar.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/footer.php';
?>
