<?php
const HASH_LEN = 60;
const SALT_LEN = 22;

function generate_salt(): string {
    $salt = str_replace('+', '.', substr(base64_encode(random_bytes(ceil(SALT_LEN * 0.75))), 0, SALT_LEN));
    echo 'salt: '.$salt;
    return $salt;
}

function generate_hash(string $plaintext): string {
    return crypt($plaintext, '$2y$11$'.generate_salt().'$');
}

function verify_password(string $plaintext, string $hash) {
    return hash_equals(crypt($plaintext, $hash), $hash);
}
?>