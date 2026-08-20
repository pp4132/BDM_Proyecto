<?php
$password = '2309.HuLK';
$hash = password_hash($password, PASSWORD_BCRYPT);
echo $hash;
?>