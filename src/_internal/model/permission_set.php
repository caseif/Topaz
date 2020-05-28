<?php
class PermissionSet {
    public bool $read;
    public bool $write;
    public bool $write_other;
    public bool $administrate_users;

    function __construct(int $mask) {
        $this->read                 = ($mask & 0x80000000) !== 0;
        $this->write                = ($mask & 0x40000000) !== 0;
        $this->write_other          = ($mask & 0x20000000) !== 0;
        $this->administrate_users   = ($mask & 0x10000000) !== 0;
    }
}
?>
