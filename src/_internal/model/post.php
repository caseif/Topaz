<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/thirdparty/parsedown.php';

class Post {
    public int $id;
    public string $title;
    public string $content_raw;
    public string $content_parsed;
    public int $create_time;
    public int $update_time;
    public ?int $author_id;
    public string $author_name;
    public ?int $category;
    public string $category_name;
    public bool $visible;
    public bool $about;

    function __construct(int $id, string $title, string $content, int $create_time, int $update_time,
            ?int $author_id, string $author_name, ?int $category, string $category_name, bool $visible, bool $about) {
        $this->id = $id;
        $this->title = $title;
        $this->content_raw = $content;
        $this->create_time = $create_time;
        $this->update_time = $update_time;
        $this->author_id = $author_id;
        $this->author_name = $author_name;
        $this->category = $category;
        $this->category_name = $category_name;
        $this->visible = $visible;
        $this->about = $about;

        $parsedown = new Parsedown();
        $this->content_parsed = $parsedown->text($this->content_raw);
    }
}
?>
