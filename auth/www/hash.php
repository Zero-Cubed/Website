<?php
header('Content-Type: text/plain');
echo password_hash($_GET['p'], PASSWORD_DEFAULT);
?>
