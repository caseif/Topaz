<?php
include_once "./util/_page_config.php";
include_once "./template/_post.php";

PageConfig::$title = "Home";

include_once "./template/_header.php";
?>

<div id="main-content">
    <?php
    $page = $_GET['page'] ?? 1;
    $posts = get_posts(($page - 1) * GlobalConfig\HOME_RECENT_POST_COUNT, GlobalConfig\HOME_RECENT_POST_COUNT, true);

    foreach ($posts as $post_index => $post) {
        render_post($post, true);
    }
    ?>
</div>

<?php
include_once "./template/_sidebar.php";
include_once "./template/_footer.php";
?>
