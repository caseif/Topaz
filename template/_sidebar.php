
<?php
$posts = get_posts(-1, -1, true, true);
?>

<div id="sidebar">
    <div class="sidebar-section">
        <div class="sidebar-header">Recent Posts</div>
        <div class="sidebar-content">
            <div class="sidebar-link">
                <ul id="post-list">
                    <?php
                    foreach ($posts as $post_index => $post) {
                    ?>
                        <li <?php echo $post_index >= GlobalConfig\SIDEBAR_RECENT_POST_COUNT ? "class=\"older\"" : ""; ?>>
                            <a href="/post.php?id=<?php echo $post->id; ?>"><?php echo $post->title; ?></a>
                        </li>
                    <?php
                    }
                    ?>
                </ul>
                <?php
                if (count($posts) > GlobalConfig\SIDEBAR_RECENT_POST_COUNT) {
                ?>
                    <div id="recent-control">
                        <div id="expand-recent" class="pseudo-link">
                            +<?php echo (count($posts) - GlobalConfig\SIDEBAR_RECENT_POST_COUNT); ?> more...
                        </div>
                        <div id="collapse-recent" class="pseudo-link" style="display:none;">
                            Hide <?php echo (count($posts) - GlobalConfig\SIDEBAR_RECENT_POST_COUNT); ?> more
                        </div>
                    </div>
                <?php
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
                    $post_year = date('Y', $post->time);
                    $post_month = date('F', $post->time);
                    $post_day = date('j', $post->time);
                    
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