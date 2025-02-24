<!-- PHP used to ensure that user is logged in -->
<?php include "./php_scripts/session.php" ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- link rel="stylesheet" href="styles/toolStyle.css" -->
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="jquery-ui.css">
    <!-- <script src="jquery-ui.css"></script> -->
<!-- -->
    <link rel="stylesheet" href="tempHAStyle.css">
    <script src="script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>House Assessment Tool</title>
    <link rel="icon" type="image/x-icon" href="/hfh-capstone/images/favicon.ico">
    <?php include "navbar.php" ?>
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

<script>
    // Does not allow numbers to be entered for first name
    document.addEventListener("DOMContentLoaded", function() {
            const firstNameInput = document.getElementById("firstname");

        firstNameInput.addEventListener("input", function() {
        this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
        });
    });

    // Does not allow numbers to be entered for last name
    document.addEventListener("DOMContentLoaded", function() {
            const lastNameInput = document.getElementById("lastname");

        lastNameInput.addEventListener("input", function() {
        this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
        });
    });

    // Does not allow numbers to be entered for the city name
    document.addEventListener("DOMContentLoaded", function() {
        const cityInput = document.getElementById("city");

        cityInput.addEventListener("input", function() {
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
        });
    });

    // Only allows letters to be entered and automatically changes them to upper case letters
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("assessment-form");
        const stateInput = document.getElementById("state");

        stateInput.addEventListener("input", function () {
            this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '').substring(0,2);
        });

    });

    // Only allows numbers for the phone number
    document.getElementById('phone').addEventListener('input', function() {
    let phone = this.value.replace(/[^\d]/g, '');

    // Formatting for the phone number (xxx-xxx-xxxx)
    if(phone.length > 10){
        phone = phone.substring(0,10);
    }
    if (phone.length > 3) {
        phone = phone.substring(0,3) + "-" + phone.substring(3);
    }
    if (phone.length > 6) {
        phone = phone.substring(0,7) + "-" + phone.substring(7);
    }

    this.value = phone;
    });

    document.getElementById('phone').addEventListener('keydown', function(e) {
        if(e.key === 'Backspace'){
            let cursorPos = this.selectionStart;
            if(cursorPos === 4 || cursorPos === 8){
                e.preventDefault();
                this.setSelectionRange(cursorPos-1,cursorPos-1);
            }
        }
    });

    // Only allows numbers to be entered for the zip code and doesn't allow you to exceed 5 numbers
    document.addEventListener("DOMContentLoaded", function() {
        const inputZipcode = document.getElementById("zip");

        inputZipcode.addEventListener("input", function() {
            let zip = this.value.replace(/\D/g, "");

            if(zip.length > 5){
                zip = zip.substring(0,5);
            }
            this.value = zip;
        });

    });

    // Checks for errors and includes the formatting for errors
    document.getElementById("assessment-form").addEventListener("submit", function(event) {
    event.preventDefault();
    let formData = new FormData(this);

    fetch("./php_scripts/form.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        document.querySelectorAll(".error-message").forEach(el => el.remove());
        document.querySelectorAll("[data-error]").forEach(el => el.removeAttribute("data-error"));

        if (data.status === "error") {
            for (const [key, value] of Object.entries(data.errors)) {
                let inputField = document.querySelector(`[name="${key}"]`);
                if (inputField) {
                    inputField.value = data.old_values[key] || "";
                    let errorSpan = document.createElement("span");
                    errorSpan.className = "error-message";
                    errorSpan.style.color = "red";
                    errorSpan.style.display = "block";
                    errorSpan.style.marginTop = "5px";
                    errorSpan.style.marginBottom = "10px";
                    errorSpan.style.fontSize = "14px";
                    errorSpan.style.fontWeight = "bold";
                    errorSpan.innerText = value;
                    inputField.insertAdjacentElement("afterend", errorSpan);
                }
            }
        } else if (data.status === "success") {
            window.location.href = `./test_page.php?id=${data.new_id}`;
        }
    })
    .catch(error => console.error("Error:", error));
});
</script>
</body>

</html>
