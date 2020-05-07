<?php
require_once "./util/db/_db_posts.php";
require_once "./util/_page_config.php";
include_once "./template/_post.php";

$post = get_about();

PageConfig::$title = $post !== null ? $post->title : 'About Me';

include_once "./template/_header.php";
?>

<main id="main-content">
    <?php
    if ($post !== null) {
        render_post($post);
    } else {
        require_once './template/_about_default.php';
    }
    ?>
</main>

<?php
include_once "./template/_sidebar.php";
include_once "./template/_footer.php";
?>
