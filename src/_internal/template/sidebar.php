<?php
const COOKIE_RECENT_EXPANDED = 'recent_expanded';

$posts = get_posts(-1, -1, true);

$recent_expanded = ($_COOKIE[COOKIE_RECENT_EXPANDED] ?? '0') === '1';
?>

<div id="sidebar">
    <div class="sidebar-section">
        <div class="sidebar-header">Recent Posts</div>
        <div class="sidebar-content">
            <div class="sidebar-link">
                <ul id="post-list" <?php echo $recent_expanded ? 'class=expanded' : ''; ?>>
                    <?php
                    foreach ($posts as $post_index => $post) {
                        $li_class = $post_index >= GlobalConfig\get_config()->sidebar_recent_post_count ? 'class="older"' : '';
                        echo <<<HTML
                        <li {$li_class}>
                            <a href="/post.php?id={$post->id}">{$post->title}</a>
                        </li>
                        HTML;
                    }
                    ?>
                </ul>
                <?php
                if (count($posts) > GlobalConfig\get_config()->sidebar_recent_post_count) {
                    $addl_posts = count($posts) - GlobalConfig\get_config()->sidebar_recent_post_count;
                    $expand_style = $recent_expanded ? 'style="display:none;"' : '';
                    $collapse_style = !$recent_expanded ? 'style="display:none;"' : '';
                    echo <<<HTML
                    <div id="recent-control">
                        <div id="expand-recent" class="pseudo-link" {$expand_style}>
                            +{$addl_posts} more...
                        </div>
                        <div id="collapse-recent" class="pseudo-link" {$collapse_style}>
                            Hide {$addl_posts} more
                        </div>
                    </div>
                    HTML;
                }
                ?>
            </div>
        </div>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-header">Archives</div>
        <div id="archive-container" class="sidebar-content">
            <?php
            $cur_year = null;
            $cur_month = null;

            if (count($posts) > 0) {
                foreach ($posts as $post_index => $post) {
                    $post_year = date('Y', $post->create_time);
                    $post_month = date('F', $post->create_time);
                    $post_day = date('j', $post->create_time);

                    if ($post_year !== $cur_year) {
                        if ($cur_year !== null) {
                            echo <<<HTML
                                        </ul> <!-- /archive-month -->
                                    </li>
                                </ul> <!-- /archive-year-body -->
                            </div> <!-- /archive-year -->
                            HTML;
                        }

                        $cur_year = $post_year;
                        $cur_month = null;

                        echo <<<HTML
                        <div class="archive-year collapsed">
                            <div class="archive-year-label">
                                <span class="archive-year-expander fas fa-caret-right"></span>
                                {$cur_year}
                            </div>
                            <ul class="archive-year-body">
                        HTML;
                    }

                    if ($post_month !== $cur_month) {
                        if ($cur_month !== null) {
                            echo <<<HTML
                                </ul> <!-- /archive-month -->
                            </li>
                            HTML;
                        }

                        $cur_month = $post_month;

                        echo <<<HTML
                        <li class="archive-month-label">{$post_month}</li>
                        <li>
                            <ul class="archive-month">
                        HTML;
                    }

                    echo <<<HTML
                    <li class="archive-item">
                        <a href="/post.php?id={$post->id}">{$post_month} {$post_day}</a>
                    </li>
                    HTML;
                }


                echo <<<HTML
                            </ul> <!-- /archive-month -->
                        </li>
                    </ul> <!-- /archive-year-body -->
                </div> <!-- /archive-year -->
                HTML;
            }
            ?>
        </div>
    </div>
    <?php
    if ($current_user !== null && $current_user->permissions->write) {
        echo <<<HTML
        <div class="sidebar-section">
            <div class="sidebar-header">Content Management</div>
            <div class="sidebar-content sidebar-links">
                <div class="sidebar-link">
                    <a href="/edit.php">Create a Post</a>
                </div>
            </div>
        </div>
        HTML;
    }
    if ($current_user !== null || GlobalConfig\get_config()->show_login_links) {
        $back_uri = $_SERVER['REQUEST_URI'];
        if ($current_user !== null) {
            echo <<<HTML
            <div class="sidebar-section">
                <div class="sidebar-header">User Management</div>
                <div id="user-links" class="sidebar-content sidebar-links">
                    <div class="sidebar-link">
                        <a href="/user/logout.php?back={$back_uri}">Log Out</a>
                    </div>
                </div>
            </div>
            HTML;
        } else {
            echo <<<HTML
            <div class="sidebar-section">
                <div class="sidebar-header">User Management</div>
                <div id="user-links" class="sidebar-content sidebar-links">
                    <div class="sidebar-link">
                        <a href="/user/login.php?back={$back_uri}">Log In</a>
                    </div>
                    <div class="sidebar-link">
                        <a href="/user/register.php?back={$back_uri}">Register</a>
                    </div>
                </div>
            </div>
            HTML;
        }
    }
    ?>
    <div class="sidebar-section">
        <div class="sidebar-header">Links</div>
        <div id="external-links" class="sidebar-content sidebar-links">
            <?php
            foreach (GlobalConfig\get_config()->external_links as $link) {
                echo $link->to_html();
            }
            ?>
        </div>
    </div>
</div>