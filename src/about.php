<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/db/db_posts.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/page_config.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/post.php';

$post = get_about();

PageConfig::$title = $post !== null ? $post->title : 'About Me';

include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/header.php';
?>

<main id="main-content">
    <?php
    if ($post !== null) {
        render_post($post);
    } else {
        require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/about_default.php';
    }
    ?>
</main>

<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/sidebar.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/footer.php';
?>
