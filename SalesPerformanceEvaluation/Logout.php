<?php
// Logout.php - destroys session and redirects to login
// Fixed: this file was missing entirely; Sign Out links pointed to '#'
session_start();
session_unset();
session_destroy();
header('Location: Index.php');
exit;
?>
