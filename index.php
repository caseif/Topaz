<?php
include_once "./template/_pager.php";
include_once "./template/_post.php";
require_once "./util/_dbconn.php";
require_once "./util/_page_config.php";

PageConfig::$title = "Home";

$page = $_GET['page'] ?? 1;

if (!is_numeric($page)) {
    $page = 1;
}

$page_count = ceil(get_post_count() / GlobalConfig\HOME_RECENT_POST_COUNT);

if ($page < 1 || $page > $page_count) {
    http_response_code(404);
    include('./error/404.php');
    die();
}

include_once "./template/_header.php";
?>

<div id="main-content">
    <div id="post-previews">
        <?php
        $posts = get_posts(($page - 1) * GlobalConfig\HOME_RECENT_POST_COUNT, GlobalConfig\HOME_RECENT_POST_COUNT, true, false);

        foreach ($posts as $post_index => $post) {
            render_post($post, true);
        }
        ?>
    </div>

    <?php
    render_pager($page, $page_count);
    ?>
</div>

<?php
include_once "./template/_sidebar.php";
include_once "./template/_footer.php";
?>
