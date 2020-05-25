<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/db/_db_posts.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/_page_config.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/_post.php';

$post = get_about();

PageConfig::$title = $post !== null ? $post->title : 'About Me';

include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/_header.php';
?>

<main id="main-content">
    <?php
    if ($post !== null) {
        render_post($post);
    } else {
        require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/_about_default.php';
    }
    ?>
</main>

<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/_sidebar.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/_footer.php';
?>
