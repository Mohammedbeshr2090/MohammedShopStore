<?php
require_once '../includes/functions.php';
session_destroy();
setcookie('user_email', '', time()-3600, '/');
redirect('../index.php');
?>
