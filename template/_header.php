<?php include_once dirname(__FILE__)."/../util/_utility.php"; ?>
<?php include_once dirname(__FILE__)."/../util/_global_config.php"; ?>
<?php include_once dirname(__FILE__)."/../util/_page_config.php"; ?>
<?php include_once dirname(__FILE__)."/../util/_dbconn.php"; ?>

<!DOCTYPE html>
<html>
<head>
    <title>
        <?php
        if (isNullOrEmpty(PageConfig::$title)) {
            echo "caseif.blog";
        } else {
            echo PageConfig::$title." - ".GlobalConfig\SITE_NAME;
        }
        ?>
    </title>
    
    <!-- third-party -->
    <link rel="stylesheet" href="/styles/thirdparty/bootstrap/bootstrap-grid.min.css">

    <!-- first-party -->
    <link rel="stylesheet" href="/styles/built/styles.css">
    <link rel="stylesheet" href="/styles/thirdparty/fontawesome/css/all.min.css">

    <!-- third-party -->
    <script type="text/javascript" src="/scripts/thirdparty/jquery/jquery-3.5.0.min.js"></script>

    <!-- first-party -->
    <script type="text/javascript" src="/scripts/built/listeners.js"></script>
</head>

<body>
    <div id="content-wrapper">
        <div id="container">
            <?php include_once dirname(__FILE__)."/../template/_navbar.php"; ?>
            <div id="main-content">
