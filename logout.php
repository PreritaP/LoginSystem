<?php
// Initialize the session
session_start();
 
// Unset all of the session variables
$_SESSION = array();
$_POST = array();
$_SESSION['loggedin'] = false;
$_SESSION['login_user'] = "";
unset($_SESSION['login_user']);
 
// Destroy the session.
session_destroy();
 
// Redirect to index page
header("location: index.php");
exit;
?>