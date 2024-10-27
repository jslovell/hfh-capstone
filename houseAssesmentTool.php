<!-- PHP used to ensure that user is logged in -->
<?php include "./php_scripts/session.php" ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/toolStyle.css">
    <link rel="stylesheet" href="jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="jquery-ui.css"></script>
    <script src="script.js"></script>
    <title>House Assesment Tool</title>
    <?php include "navbar.php" ?>

<!--
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("assessment-form");
            form.addEventListener("submit", function(event) {
                event.preventDefault();

                const formData = new FormData(form);

                fetch("https://script.google.com/macros/s/AKfycbwkPwPW1KXsqGyisNC9lIG3dHKrhl7eoN_OnDgd-vsaPV9kdo29NiiIbV2AM-Jw9s26/exec", {
                    method: "POST",
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {

                        if (data.result === "success") {
                            alert("Form submitted successfully!");
                        } else {
                            alert("Form submission failed. Error: " + data.error);
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Form submission failed. Error: " + error.message);
                    });
            });
        });
    </script>

-->

</head>
<body style="margin-top: 8%">
<h1>House Assessment Tool</h1>
<form id="assessment-form" action="./php_scripts/form.php" method="post" enctype="multipart/form-data">
<!--
    <label for="photoupload">Upload Photo (PNG or JPG):</label>
    <input type="file" name="photoupload" id="photoupload" accept="image/jpeg,image/png" required>
    <textarea name="annotation" id="annotation" placeholder="Add comments"></textarea>
-->

    <h2>Client Information</h2>

    <!-- Sections for contact info -->
    <label for="firstname">First Name:</label>
    <input type="text" name="firstname" id="firstname" maxlength="32" required>
    <label for="lastname">Last Name:</label>
    <input type="text" name="lastname" id="lastname" maxlength="32" required>
    <label for="email">Email Address (Optional):</label>
    <input type="email" name="email" id="email" maxlength="64" >
    <label for="phone">Phone Number:</label>
    <input type="tel" name="phone" id="phone" maxlength="16" required>
    <label for="address">Address:</label>
    <input type="text" name="address" id="address" maxlength="64" required>
    <label for="city">City:</label>
    <input type="text" name="city" id="city" maxlength="32" required>
    <label for="state">State:</label>
    <input type="text" name="state" id="state" maxlength="32" required>

    <label for="zip">Zip Code:</label>
    <input type="text" name="zip" id="zip" maxlength="8" required>

    <label for="layout">Home Layout:</label>
    <input type="file" name="layout" id="layout">

    <br>
    <button type="submit" name="submit">Submit Assessment</button>

</form>
<div id="output"></div>
</body>
</html>
