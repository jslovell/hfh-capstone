<!-- PHP used to ensure that user is logged in -->
<?php include "./php_scripts/session.php" ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- link rel="stylesheet" href="styles/toolStyle.css" -->
    <link rel="stylesheet" href="jquery-ui.css">
    <!-- <script src="jquery-ui.css"></script> -->
<!-- -->
    <link rel="stylesheet" href="tempHAStyle.css">
    <script src="script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>House Assessment Tool</title>
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
    <body class="background">
    <div class="card">
        <container class="container">
            <h1 class="text-center header">House Assessment Tool</h1>
            <form id="assessment-form" action="./php_scripts/form.php" method="post" enctype="multipart/form-data">
            <!--
                <label for="photoupload">Upload Photo (PNG or JPG):</label>
                <input type="file" name="photoupload" id="photoupload" accept="image/jpeg,image/png" required>
                <textarea name="annotation" id="annotation" placeholder="Add comments"></textarea>
            -->

            <!-- Sections for contact info -->
             <div class ="inputparam text-center">
                <label for="firstname">First Name:</label>
                <div class ="box">
                <input type="text" name="firstname" id="firstname" maxlength="32" required placeholder="Joe">
                </div>
            </div>
            <div class ="inputparam text-center">
                <label for="lastname">Last Name:</label>
                <div class ="box">
                <input type="text" name="lastname" id="lastname" maxlength="32" required placeholder="Smith">
                </div>
            </div>
            <div class ="inputparam text-center">
                <label for="email">Email Address (Optional):</label>
                <div class ="box">
                    <!-- previous type was email -->
                <input type="text" name="email" id="email" maxlength="64" placeholder="example@gmail.com">
                </div>
            </div>
            <div class ="inputparam text-center">
                <label for="phone">Phone Number:</label>
                <div class ="box">
                    <!-- previous type was tel -->
                <input type="text" name="phone" id="phone" maxlength="16" required placeholder="555-555-5555">
                </div>
            </div>
            <div class ="inputparam text-center">
                <label for="address">Address:</label>
                <div class ="box">
                <input type="text" name="address" id="address" maxlength="64" required placeholder="832 N State Drive">
                </div>
            </div>
            <div class ="inputparam text-center">
                <label for="city">City:</label>
                <div class ="box">
                <input type="text" name="city" id="city" maxlength="32" required placeholder="St. Louis, Peoria, etc.">
                </div>
            </div>
            <div class ="inputparam text-center">
                <label for="state">State:</label>
                <div class ="box">
                <input type="text" name="state" id="state" maxlength="32" required placeholder="Missouri, Illinois, etc.">
                </div>
            </div>
            <div class ="inputparam text-center">
                <label for="zip">Zip Code:</label>
                <div class ="box">
                <input type="text" name="zip" id="zip" maxlength="8" required placeholder="55555">
                </div>
            </div>
            <div class ="inputparam text-center">
                <label for="layout">Home Layout:</label>
                <div class ="box">
                <input type="file" name="layout" id="layout">
                </div>
            </div>
            <div class ="inputparam text-center">
                <div class ="box">
                <button type="submit" name="submit">Submit Assessment</button>
                </div>
            </div>
            </form>
            <div id="output"></div>
        </container>
    </div>
</body>

</html>