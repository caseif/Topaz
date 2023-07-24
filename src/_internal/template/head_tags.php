<?php
$page_title = '';
$page_desc = '';
$page_type = '';
$page_url = '';
$page_image_url = '';

$cur_post = PageConfig::$post;

if ($cur_post != null) {
    // post page

    require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/post_util.php';
    
    $page_title = $cur_post->title.' &ndash; '.GlobalConfig\get_config()->content->site_title;

    $page_desc = strip_tags(
        preg_replace('/\r?\n/', ' ',
            abridge_text(
                $cur_post->content_parsed, GlobalConfig\get_config()->display->post_social_chars
            )
        )
    );

    $page_type = 'article';

    $page_url = '/content/post.php?id='.$cur_post->id;

    //TODO: eventually we'll have optional per-article images that we retrieve from the db
    $page_image_url = GlobalConfig\get_config()->content->site_image;
} else {
    // all other pages

    $page_title = GlobalConfig\get_config()->content->site_title;
    $page_desc = GlobalConfig\get_config()->content->site_description;
    $page_type = 'website';
    $page_url = $_SERVER['REQUEST_URI'];
    $page_image_url = GlobalConfig\get_config()->content->site_image;
}

if (!is_null_or_empty($page_image_url)) {
    $page_image_url = to_server_url($page_image_url);
} else {
    $page_image_url = null;
}

$page_url = to_server_url($page_url);

$page_favicon = GlobalConfig\get_config()->content->site_icon;

echo <<<HTML
    <title>{$page_title}</title>

    <meta property="og:title" content="{$page_title}" />
    <meta property="og:description" content="{$page_desc}" />
    <meta property="og:type" content="{$page_type}" />
    <meta property="og:url" content="{$page_url}" />
HTML;

foreach (GlobalConfig\get_config()->content->rel_me_links as $link) {
    echo "\n    ".$link->to_html()."\n";
}

if ($page_image_url != null) {
    echo <<<HTML
        <meta property="og:image" content="{$page_image_url}" />
    HTML;
}

if ($cur_post != null) {
    $create_time_8601 = date('c', $cur_post->create_time);
    $update_time_8601 = $cur_post->update_time != -1 ? date('c', $cur_post->update_time) : $create_time_8601;
    $section = $cur_post->category_name;

    echo <<<HTML
        <meta property="article:published_time" content="{$create_time_8601}" />
        <meta property="article:modified_time" content="{$update_time_8601}" />
        <meta property="article:author" content="article" />
        <meta property="article:section" content="{$section}" />
    HTML;
}

if (!is_null_or_empty($page_favicon)) {
    $page_favicon = to_server_url($page_favicon);
    echo <<<HTML
        <link rel="icon" href="{$page_favicon}" />
    HTML;
}
