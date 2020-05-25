<?php
class User {
    public int $id;
    public string $username;
    public string $full_name;
    public int $register_time;
    public bool $active;
    public bool $admin;
 
    function __construct(int $id, string $username, string $full_name, int $register_time, bool $active,
            bool $admin) {
        $this->id = $id;
        $this->username = $username;
        $this->full_name = $full_name;
        $this->register_time = $register_time;
        $this->active = $active;
        $this->admin = $admin;
    }
}
?>
