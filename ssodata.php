<?php
include('sso.php');
echo "<pre>";
print_r($user);
print_r($auth);
print_r("reg" . $user->data["is_registered"]);
echo "\r\n";
print_r("bot" . $user->data["is_bot"]);
echo "</pre>";
?>