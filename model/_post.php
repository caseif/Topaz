<?php
class Post {
    public int $id;
    public string $title;
    public string $content;
    public int $time;
    public int $author;
    public string $category;
    public bool $visible;

    function __construct(int $id, string $title, string $content, int $time,
            int $author, string $category, bool $visible) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->time = $time;
        $this->author = $author;
        $this->category = $category;
        $this->visible = $visible;
    }
}
?>
