<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="styles/toolStyle.css" />
    <link rel="stylesheet" href="styles/navbar.css" />
    <link rel="stylesheet" href="styles/indexStyle.css" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="script.js"></script>
    <title>Create New User</title>
    <link rel="icon" type="image/x-icon" href="/hfh-capstone/images/favicon.ico" />
    <style>
        
        .submitButton {
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
            margin-top: 10px;
        }
        .error-message {
            color: red;
            font-size: 14px;
            font-weight: bold;
            display: block; /* Ensure each error is on a new line */
        }

        #uname-error{
            margin-bottom: 10px;
        }
    </style>
</head>

<?php include "navbar.php"; ?>

<body>
<form id="login-form" action="./php_scripts/add_user.php" method="post">
    <div class="container">
        <h2>Create New User</h2>
        
        <label for="uname"><b>New Username</b></label>
        <input type="text" name="uname" id="uname" placeholder="Enter Username" required />
        <span class="error-message" id="uname-error"></span> <!-- Error message for username -->

        <label for="psw"><b>New Password</b></label>
        <input type="password" name="psw" id="psw" placeholder="Password must have 8+ characters, 1+ capital, 1+ number, and 1+ symbol" required />
        <span class="error-message" id="psw2-error"></span> <!-- Error message for password -->

        <label for="psw2"><b>Confirm Password</b></label>
        <input type="password" name="psw2" id="psw2" placeholder="Repeat Password" required />
        <span class="error-message" id="psw-error"></span> <!-- Error message for confirm password -->

        <button type="submit" class="submitButton">Create New User</button>
        <button type="button" class="submitButton" onclick="window.location.href='index.php'">
            Return to Login
        </button>
    </div>
</form>

<script>
document.getElementById("login-form").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent the form from submitting the traditional way

    let formData = new FormData(this);

    fetch("./php_scripts/add_user.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Clear previous error messages
        document.querySelectorAll(".error-message").forEach(el => el.innerHTML = "");

        if (data.status === "error") {
            // Loop through errors and display them in the respective error containers
            for (const [key, messages] of Object.entries(data.errors)) {
                let errorContainer = document.getElementById(`${key}-error`);

                if (errorContainer) {
                    // Clear any existing messages for this field
                    errorContainer.innerHTML = '';

                    // Loop through all messages for the field and display them
                    messages.forEach(message => {
                        let errorSpan = document.createElement("span");
                        errorSpan.className = "error-message";
                        errorSpan.innerText = message;
                        errorContainer.appendChild(errorSpan); // Append each error message to the container
                    });
                }
            }
        } else if (data.status === "success") {
            window.location.href = `./index.php`; // Redirect on success
        }
    })
    .catch(error => console.error("Error:", error));
});
</script>
</body>
</html>
