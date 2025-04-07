<?php
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ./index.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);
$publicPages = ['index.php', 'login.php', 'login_screen.php', 'new_user.php', 'add_user.php'];

if (!in_array($currentPage, $publicPages)) {
    if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
        header('Location: ./index.php');
        exit();
    }
}
?>
