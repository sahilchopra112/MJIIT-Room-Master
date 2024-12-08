<?php
// Replace 'password123' with the password you want to hash
$password = 'password123';
echo password_hash($password, PASSWORD_DEFAULT);
?>
