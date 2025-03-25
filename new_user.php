<?php require_once './php_scripts/session.php'; ?>

<!DOCTYPE html>
<?php

?>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles/toolStyle.css">
        <link rel="stylesheet" href="styles/navbar.css">
        <link rel="stylesheet" href="jquery-ui.css">
        <link rel="stylesheet" href="styles/indexStyle.css">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="jquery-ui.css"></script>
        <script src="script.js"></script>
        <title>Login</title>
        <link rel="icon" type="image/x-icon" href="/hfh-capstone/images/favicon.ico">
    </head>
    <style>
        .container
        {
            background-color: #bfbfbf;
            border: 1px solid #bfbfbf;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 1);
        }

        .submitButton{
            background-color: #09c;
            color: white;
            border: none;
            width: 100%;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 25px;
            transition: background-color 0.2s, transform 0.1s;
            height: 35px;
            text-align: center;
        }
        
    </style>
    <?php include "navbar.php" ?>
<body>


<!-- ONLY change made to this document was changing action from "appMenu.php to /php_scripts/login.php-->
<form action="./php_scripts/add_user.php" method="post">

    <div class="container">
        <label for="uname"><b>New Username</b></label>
        <input type="text" placeholder="Enter Username" name="uname" required>

        <label for="psw"><b>New Password</b></label>
        <input type="password" placeholder="Password must have 8+ characters, 1+ capital, 1+ number, and 1+ symbol" name="psw" required>

	<label for="psw2"><b>Confirm Password</b></label>
	<input type="password" placeholder="Repeat Password" name="psw2" required>

        <button type="submit" name="submit" class="submitButton">Create New User</button>

    </div>
</form>

</body>


</html>
