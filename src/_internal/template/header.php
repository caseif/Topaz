<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/global_config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/page_config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/db/db_posts.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>
        <?php
        if (is_null_or_empty(PageConfig::$title)) {
            echo GlobalConfig\get_config()->content->site_title;
        } else {
            echo PageConfig::$title." - ".GlobalConfig\get_config()->content->site_title;
        }
        ?>
    </title>
    
    <!-- third-party -->
    <link rel="stylesheet" href="/styles/thirdparty/bootstrap/bootstrap-grid.min.css">

    <!-- first-party -->
    <link rel="stylesheet" href="/styles/built/styles.css">
    <link rel="stylesheet" href="/styles/thirdparty/fontawesome/css/all.min.css">
    
    <!-- fonts (could be first- or third-party depending on config) -->
    <?php
    if (GlobalConfig\get_config()->internal->use_google_fonts) {
    ?>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap"
            rel="stylesheet">
    <?php
    } else {
    ?>
    <link href="/fonts/lato/css/lato.css" rel="stylesheet">
    <?php
    }
    ?>

    <!-- third-party -->
    <script type="text/javascript" src="/scripts/thirdparty/jquery/jquery-3.5.0.min.js"></script>

    <!-- first-party -->
    <script type="text/javascript" src="/scripts/built/listeners.js"></script>
</head>

<body>
    <div id="container">
        <?php
        include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/navbar.php';
        ?>
        <div id="page-body">
