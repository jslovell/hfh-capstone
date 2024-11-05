<!DOCTYPE html>
<?php include "./php_scripts/session.php" ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>House Assessment Tool</title>
    <link rel="stylesheet" href="jquery-ui.css">
    <link rel="stylesheet" href="./styles/tabToolStyle.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="jquery-ui.js"></script>
    <script src="script.js"></script>

<style>
    #clearButton {
        background-image: url("images/clear-button.png");
    }
    #select-button {
        background-image: url("images/select-button.png");
    }
    #alert-severe-button {
        background-image: url("images/alert-severe-button.png");
    }
    #alert-moderate-button {
        background-image: url("images/alert-moderate-button.png");
    }
    #alert-button {
        background-image: url("images/alert-button.png");
    }
    #photo-button {
        background-image: url("images/picture-button.png");
    }
    #note-button {
        background-image: url("images/note-button.png");
    }
    #clearButton, #select-button, #photo-button, #alert-moderate-button, #alert-severe-button, #alert-button, #removal-button, #note-button {
        background-repeat: no-repeat;
        background-size: contain;
        width: 58px;
        height: 58px;
        background-color: inherit;
    }


    .alert-severe-icon {
        background-image: url("images/alert-sever-icon.png");
    }
    .alert-moderate-icon {
        background-image: url("images/alert-moderate-icon.png");
    }
    .alert-icon {
        background-image: url("images/alert-icon.png");
    }
    .picture-icon {
        background-image: url("images/picture-icon.png");
    }
    .note-icon {
        background-image: url("images/note-icon.png");
    }

    .alert-icon, .alert-moderate-icon, .alert-severe-icon, .note-icon, .picture-icon {
        position: absolute;
        cursor: pointer;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        width: 32px;
        height: 32px;
        pointer-events: none;
    }
    #removal-button {
        background-image: url("images/removal-button.png");

    }
    .picture{
        display:none;
        width:30%;
        height:30%;
    }
    .box, .box-alert, .box-note {

        position: absolute;
        width: 32px;
        height: 32px;
    }


    .clickableArea {
        width: 800px;
        height: 800px;
        margin: auto;
        border: 3px dashed black;
        display: flex;
        align-items: center;
        justify-content: center;;
    }
    .sidebar {
        position: fixed;
        top: 54%;
        left: 0;
        transform: translateY(-50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        background-color: #f1f1f1;
        border-right: 1px solid #ccc;
        border-radius: 10px;
        padding: 2px;
        z-index: 1;
    }

    .sidebar-button {
        background-color: transparent;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 40px;
        height: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }

    .sidebar-button img {
        max-width: 80%;
        max-height: 80%;
    }


    @media screen and (max-width: 600px) {
        .sidebar {
            width: 60px;
        }
        .sidebar button {
            width: 30px;
            height: 30px;
        }
    }
</style>

</head>
<?php include "navbar.php" ?>
<?php
require_once "./php_scripts/db.php";

// Gets the ID from the URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Checks the ID and makes sure it is valid
if ($id > 0) {
    $sql = "SELECT layout FROM form_entries WHERE id = $id";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $layout_path = "./uploads/layouts/" . $row['layout'];
    } else {
        echo "Image not found";
        $layout_path = "./assets/testbp.png";
    }
} else {
    echo "Invalid ID";
    $layout_path = "./assets/testbp.png";
}

mysqli_close($conn);
?>


<body style="margin-top: 8%;">

<h1 style="font-size:250%">House Assessment Tool</h1>
<br>
<img src="images/icon-legend.png" style="width: 500px; height: 250px; justify-content: center; display: flex; align-items: center;margin: auto">

<br>
<br>
    <div class="clickableArea" >
        <?php if (isset($layout_path)) : ?>
        <img src="<?php echo $layout_path; ?>" alt="Home Layout" id="testBlueprint" style="width: 800px; height: 800px">
        <?php endif; ?>
    </div>
<br>

<div class="sidebar">
    <button id="clearButton"></button>
    <button id="select-button"></button>
    <button id="alert-severe-button"></button>
    <button id="alert-moderate-button"></button>
    <button id="alert-button"></button>
    <button id="photo-button"></button>
    <button id="note-button"></button>
</div>

</body>

</html>
