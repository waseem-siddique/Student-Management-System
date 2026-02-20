<?php
session_start();

/* =========================
   DESTROY SESSION COMPLETELY
========================= */

/* Unset all session variables */
$_SESSION = [];

/* Destroy session */
session_destroy();

/* Prevent browser caching old session */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

/* Redirect to login */
header("Location: login.php");
exit();
?>
