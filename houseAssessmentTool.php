<!-- PHP used to ensure that user is logged in -->
<?php include "./php_scripts/session.php" ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/toolStyle.css">
    <link rel="stylesheet" href="jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="script.js"></script>
    <title>House Assessment Tool</title>
    <link rel="icon" type="image/x-icon" href="/hfh-capstone/images/favicon.ico">
    <?php include "navbar.php" ?>
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
    <label for="phone">Phone Number: (xxx-xxx-xxxx) </label>
    <input type="tel" name="phone" id="phone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required>
    <label for="address">Address:</label>
    <input type="text" name="address" id="address" maxlength="64" required>
    <label for="city">City:</label>
    <input type="text" name="city" id="city" maxlength="32" required>
    <label for="state">State:</label>
    <input type="text" name="state" id="state" maxlength="2" required>
    <label for="zip">Zip Code:</label>
    <input type="text" name="zip" id="zip" maxlength="6" required>
    <label for="layout">Home Layout: 
        <a href="https://www.assess.co.polk.ia.us/cgi-bin/web/tt/infoqry.cgi?tt=home/index" target="_blank">Polk County Assessor</a>
    </label>
    <input type="file" name="layout" id="layout">
    <button type="submit" name="submit">Submit Assessment</button>

</form>
<div id="output"></div>

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
