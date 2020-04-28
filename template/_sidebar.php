<div id="sidebar">
    <div class="sidebar-section">
        <div class="sidebar-header">Recent Posts</div>
        <div class="sidebar-content">
            <div class="sidebar-link">
                <ul id="post-list">
                    <?php
                    $posts = get_posts(-1, -1, true, true);
                    foreach ($posts as $post_index => $post) {
                    ?>
                        <li <?php echo $post_index > GlobalConfig\RECENT_POST_COUNT ? "class=\"older\"" : ""; ?>>
                            <a href="/post.php?id=<?php echo $post->id; ?>"><?php echo $post->title; ?></a>
                        </li>
                    <?php
                    }
                    ?>
                </ul>
                <?php
                if (count($posts) > GlobalConfig\RECENT_POST_COUNT) {
                ?>
                    <div id="recent-control">
                        <div id="expand-recent" class="pseudo-link">
                            +<?php echo (count($posts) - GlobalConfig\RECENT_POST_COUNT); ?> more...
                        </div>
                        <div id="collapse-recent" class="pseudo-link" style="display:none;">
                            Hide <?php echo (count($posts) - GlobalConfig\RECENT_POST_COUNT); ?> more
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
        <div class="sidebar-content">
            <!-- TODO -->
        </div>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-header">Links</div>
        <div class="sidebar-content">
            <div class="sidebar-link">
                <a href="https://caseif.net">Landing Page</a>
            </div>
            <div class="sidebar-link">
                <a href="https://github.com/caseif">GitHub</a>
            </div>
        </div>
    </div>
</div>