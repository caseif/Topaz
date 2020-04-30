<?php
class Post {
    public int $id;
    public string $title;
    public string $content;
    public int $time;
    public int $author_id;
    public string $author_name;
    public string $category;
    public bool $visible;

    function __construct(int $id, string $title, string $content, int $time,
            int $author_id, string $author_name, string $category, bool $visible) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->time = $time;
        $this->author_id = $author_id;
        $this->author_name = $author_name;
        $this->category = $category;
        $this->visible = $visible;
    }
}
?>
