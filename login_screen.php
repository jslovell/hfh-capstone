<?php require_once './php_scripts/session.php'; ?>

<!DOCTYPE html>
<?php

?>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="jquery-ui.css">
        <link rel="stylesheet" href="styles/indexStyle.css">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="jquery-ui.css"></script>
        <script src="script.js"></script>
        <title>Login</title>

    </head>
    <?php include "navbar.php"; ?>
<body>


<!-- ONLY change made to this document was changing action from "appMenu.php to /php_scripts/login.php-->
<form action="./hfh-capstone/php_scripts/login.php" method="post">

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
    <button type="button" class="cancelbtn" onclick="location.href='./index.php'" id="cancel-button">Cancel</button>
   <!-- <button type"button" class="cancelbtn">New User<a href="https://hfh-capstone.bradley.edu/new_user"></a>
	-->
	<span class="psw"><a href="#">Forgot password?</a></span>
    </div>
</form>

</body>


</html>
