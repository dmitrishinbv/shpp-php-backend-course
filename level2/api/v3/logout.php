<?php
if(!isset($_SESSION)){
    session_start();
}
unset ($_SESSION['hash']);
unset ($_SESSION['status']);
setcookie("session_id", "", time() - 3600 * 24 * 30 * 12, "/");
setcookie("user_id", "", time() - 3600 * 24 * 30 * 12, "/");
?>