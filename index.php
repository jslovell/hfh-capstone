<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="jquery-ui.css">
        <link rel="stylesheet" href="styles/indexStyle.css">
        <link rel="stylesheet" href="Modal.css">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="jquery-ui.js"></script>
        <script src="script.js"></script>
        <title>Login</title>
    </head>
    <?php include "navbar.php"; ?>
<body>

<!-- Login Form -->
<form action="./php_scripts/login.php" method="post">
    <div class="container">
        <label for="uname"><b>Username</b></label>
        <input type="text" placeholder="Enter Username" name="uname" required>

        <label for="psw"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="psw" required>

        <button type="submit" name="submit" id="login-button">Login</button>
        <label>
            <input type="checkbox" checked="checked" name="remember"> Remember me

            <span class="psw"><a href="./new_user.php">New user?</a></span>
        </label>
    </div>

    <div class="container" style="background-color: whitesmoke">
        <button type="button" class="cancelbtn" onclick="location.href='./homepage.php'" id="cancel-button">Cancel</button>
        <span class="psw"><a href="#">Forgot password?</a></span>
    </div>
</form>

<!-- Code Below is for the image to pop out when clicked inside the edit icon pop up (Can't get that working as of now)-->
<!-- Modal for Enlarged Image
<div id="image-modal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="modal-image" alt="Enlarged icon">
</div> -->

</body>
</html>
