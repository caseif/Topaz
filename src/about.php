<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/db/db_posts.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/page_config.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/post.php';

$about_post = get_about();

PageConfig::$title = $about_post !== null ? $about_post->title : 'About Me';
PageConfig::$post = $about_post;

include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/header.php';
?>

<main id="main-content">
    <?php
    if ($about_post !== null) {
        render_post($about_post);
    } else {
        require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/about_default.php';
    }
    ?>
</main>

<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/sidebar.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/footer.php';
?>
