<!-- PHP used to ensure that user is logged in -->
<?php include "./php_scripts/session.php" ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>House Assessment Tool</title>
    <link rel="stylesheet" href="jquery-ui.css">
    <link rel="stylesheet" href="/styles/tabToolStyle.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="jquery-ui.js"></script>
    <script src="script.js"></script>

<style>
    #customButton{
        padding: 5px;
        background-color: rgb(154, 192, 243);
        color:rgb(0, 0, 0) ;
        align-items: center;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        width: 150px;
        height: 90px;
    }

    #customButton img{
        max-width: 50px;
        margin-right: 15px;
        vertical-align: middle;
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
    .addition-icon {
        background-image: url("images/addition-icon.png");
    }
    body {
        cursor: default;
        position: relative;
    }

    #clearButton, #select-button, #photo-button, #alert-moderate-button, #alert-severe-button, #alert-button, #removal-button, #note-button {
        position: fixed;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        width: 58px;
        height: 58px;
        background-color: inherit;
    }

    #clearButton {
        margin-top: auto;
        background-image: url("images/clear-button.png");

    }

    #select-button {
        margin-top: 3%;
        background-image: url("images/select-button.png");
    }

    #alert-severe-button {
        margin-top: 7%;
        background-image: url("images/alert-severe-button.png");
    }
    #alert-moderate-button {
        margin-top: 11%;
        background-image: url("images/alert-moderate-button.png");

    }
    #alert-button {
        margin-top: 15%;
        background-image: url("images/alert-button.png");
    }
    #photo-button {
        margin-top: 19%;
        background-image: url("images/picture-button.png");
    }
    #note-button {
        margin-top: 23%;
        background-image: url("images/note-button.png");
    }
    #removal-button {
        background-image: url("images/removal-button.png");
        margin-top: 27%;
    }
    .picture{
        display:none;
        width:30%;
        height:30%;
    }
    .box {
        position: absolute;

        width: 32px;
        height: 32px;
    }
    .clickableArea {
        width: 700px;
        height: 700px;
        border: 3px dashed;
        margin: 20px auto;
        position: relative;
    }
</style>
</head>
<?php include "navbar.php" ?>
<body style="margin-top: 8%;">

<h1 style=font-size:250%>Testing Page</h1>

<button id = "customButton"  onclick="alert('You clicked this button!!')" >
    <img src="https://www.pngfind.com/pngs/b/44-446615_png-file-svg-paint-bucket-icon-png-transparent.png" alt="Custom Icon">
    Asbestos
</button>

<button id = "customButton"  onclick="alert('You clicked this button!!')" >
    <img src="https://cdn-icons-png.flaticon.com/512/2886/2886370.png" alt="Custom Icon">
    Window
</button>

<button id = "customButton"  onclick="alert('You clicked this button!!')" >
    <img src="https://static.vecteezy.com/system/resources/previews/018/874/633/original/light-bulb-icon-line-transparent-background-png.png" alt="Custom Icon">
    Electrical
</button>

<button id = "customButton"  onclick="alert('You clicked this button!!')" >
    <img src="https://cdn-icons-png.flaticon.com/512/312/312971.png" alt="Custom Icon">
    Plumbing
</button>

<button id = "customButton"  onclick="alert('You clicked this button!!')" >
    <img src="https://static.thenounproject.com/png/2280629-200.png" alt="Custom Icon">
    Heat
</button>

<br>
<button id="clearButton"></button>
<br>
<button id="select-button"></button>
<button id="alert-severe-button"></button>
<button id="alert-moderate-button"></button>
<button id="alert-button"></button>
<button id="photo-button"></button>
<button id="note-button"></button>

<button id="removal-button"></button>



<br>
        <div class="box">
            <div class="photo-icon">
                <img src="images/picture-icon.png">
            </div>
            <div class="picture">Photo Here...</div>
        </div>
<br>
<br>
<div class="clickableArea"></div>



        <br>

</body>

</html>
