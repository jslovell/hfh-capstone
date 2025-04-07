<?php require_once './php_scripts/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="jquery-ui.css">
        <link rel="stylesheet" href="styles/navbar.css">
        <link rel="stylesheet" href="styles/index.css">
        <link rel="stylesheet" href="styles/indexStyle.css">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <!--<script src="jquery-ui.css"></script>-->
        <script src="script.js"></script>
        <title>Login</title>
        <link rel="icon" type="image/x-icon" href="/hfh-capstone/images/favicon.ico">
    <style>
        .container
        {
            background-color: #bfbfbf;
            border: 1px solid #bfbfbf;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 1);
        }
        .error-message {
            color: red;
            font-size: 14px;
            font-weight: bold;
            display: block; 
        }

        #uname-error{
            margin-bottom: 10px;
        }
    </style>
    </head>
    <?php include "navbar.php"; ?>
<body>

<!-- Login Form -->
<form action="./php_scripts/login.php" id="login-form" method="post">
    <div class="container">
        <label for="uname"><b>Username</b></label>
        <input type="text" placeholder="Enter Username" name="uname" id = "uname" required>
        <span class="error-message" id="uname-error"></span>

        <label for="psw"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="psw" id="psw" required>
        <span class="error-message" id="psw-error"></span>

        <button type="submit" name="submit" id="login-button" >Login</button>
        <label>
            <input type="checkbox" checked="checked" name="remember"> Remember me
    	</label>
    </div>

    <div class="container">
        <button type="button" class="cancelbtn" onclick="location.href='./index.php'" id="cancel-button">Cancel</button>
	<span class="psw">
        <a href="./new_user.php">New user?</a>
        <a href="#">Forgot password?</a>
    </span>
    </div>
</form>
<script>
    document.getElementById("login-form").addEventListener("submit", function(event) {
        event.preventDefault();

        let formData = new FormData(this);

        fetch("./php_scripts/login.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.querySelectorAll(".error-message").forEach(el => el.innerHTML = "");

            if (data.status === "error") {
                for (const [key, messages] of Object.entries(data.errors)) {
                    let errorContainer = document.getElementById(`${key}-error`);

                    if (errorContainer) {
                        errorContainer.innerHTML = '';
                        messages.forEach(message => {
                            let errorSpan = document.createElement("span");
                            errorSpan.className = "error-message";
                            errorSpan.innerText = message;
                            errorContainer.appendChild(errorSpan);
                        });
                    }
                }
            } else if (data.status === "success") {
                window.location.href = "./catalog.php"; // Redirect to catalog on success
            }
        })
        .catch(error => console.error("Login error:", error));
    });
</script>
</body>
</html>
