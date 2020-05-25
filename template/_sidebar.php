
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
                        $li_class = $post_index >= GlobalConfig\SIDEBAR_RECENT_POST_COUNT ? 'class="older"' : '';
                        echo <<<HTML
                        <li {$li_class}>
                            <a href="/post.php?id={$post->id}">{$post->title}</a>
                        </li>
                        HTML;
                    }
                    ?>
                </ul>
                <?php
                if (count($posts) > GlobalConfig\SIDEBAR_RECENT_POST_COUNT) {
                    $addl_posts = count($posts) - GlobalConfig\SIDEBAR_RECENT_POST_COUNT;
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
                            HTML;
                        }

                        $cur_month = $post_month;

                        echo <<<HTML
                        <li class="archive-month-label">{$post_month}</li>
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
                    </ul> <!-- /archive-year-body -->
                </div> <!-- /archive-year -->
                HTML;
            }
            ?>
        </div>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-header">Links</div>
        <div id="external-links" class="sidebar-content">
            <?php
            foreach ($EXTERNAL_LINKS as $link) {
                echo $link->to_html();
            }
            ?>
        </div>
    </div>
</div>