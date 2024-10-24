<?php
    session_start();

    if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
        header('Location: ./index.php');
        exit();
    }
    if(isset($_GET['logout'])) {
        session_destroy();
        header('Location: ./index.php');
    }
?>
