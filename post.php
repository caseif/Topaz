<?php
include_once "./util/_dbconn.php";
include_once "./util/_page_config.php";
include_once "./template/_post.php";

$post = get_post($_GET['id']);

PageConfig::$title = $post->title;

include_once "./template/_header.php";

if ($post === null) {
    http_response_code(404);
    include('/error/404.php');
    die();
}
?>

<main id="main-content">
    <?php
    render_post($post);
    ?>
</main>

<?php
include_once "./template/_sidebar.php";
include_once "./template/_footer.php";
?>
