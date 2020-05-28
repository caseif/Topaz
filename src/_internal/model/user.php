<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/model/permission_set.php';

class User {
    public int $id;
    public string $username;
    public string $full_name;
    public int $register_time;
    public bool $active;
    public PermissionSet $permissions;
 
    function __construct(int $id, string $username, string $full_name, int $register_time, bool $active,
            int $perm_mask) {
        $this->id = $id;
        $this->username = $username;
        $this->full_name = $full_name;
        $this->register_time = $register_time;
        $this->active = $active;
        $this->permissions = new PermissionSet($perm_mask);
    }
}
?>
