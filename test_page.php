<?php
include "./php_scripts/session.php";
require_once "./php_scripts/db.php";

// Fetch assignment ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$assignmentID = $id;

// Fetch layout image and icons based on assignment ID
$layout_path = "./assets/testbp.png"; // Default path
if ($id > 0) {
    $sql = "SELECT layout FROM form_entries WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $layout_path = "./uploads/layouts/" . $row['layout'];
    } else {
        echo "Image not found";
    }
}
$icons = [];
if ($id > 0) {
    $sql = "SELECT * FROM icons WHERE assignmentID = $id";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $icons[] = $row;
    }
}
// Fetch current assessment status
$assessmentStatus = "Unknown"; // Default value
if ($id > 0) {
    $sql = "SELECT assessmentStatus FROM form_entries WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $assessmentStatus = $row['assessmentStatus'];
    }
}

// Fetch Address
$address = "Unknown";
if($id > 0){
    $sql = "SELECT address, city, state FROM form_entries WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    if($result && mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        $address = htmlspecialchars($row['address'] . '. ' . $row['city'] . ', ' . $row['state']);
    }
}

// Fetch Name
if($id > 0){
    $sql = "SELECT firstname, lastname FROM form_entries WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    if($result && mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            $name = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>House Assessment Tool</title>
    <link rel="stylesheet" href="jquery-ui.css">
    <link rel="stylesheet" href="./styles/toolStyle.css">
    <link rel="stylesheet" href="./styles/tabToolStyle.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="jquery-ui.js"></script>
    <link rel="icon" type="image/x-icon" href="/hfh-capstone/images/favicon.ico">
    <script>
        const assignmentID = <?php echo json_encode($assignmentID); ?>;
    </script>

    <script src="script.js"></script>
    <style>
        #clearButton {
            background-image: url("images/clear-button.png");
        }
        #select-button {
            background-image: url("images/select-button.png");
        }
        #alert-severe-button {
            background-image: url("images/alert-severe-button.png");
        }

        #clearButton, #select-button, #alert-severe-button {
            background-repeat: no-repeat;
            background-size: contain;
            width: 58px;
            height: 58px;
            background-color: inherit;
        }


        .alert-severe-icon {
            background-image: url("images/alert-sever-icon.png");
        }

        .alert-severe-icon {
            position: relative;
            cursor: pointer;
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            width: 32px;
            height: 32px;
            pointer-events: auto;
        }

        .picture{
            display:none;
            width:30%;
            height:30%;
        }
        .box, .box-alert, .box-note {

            position: absolute;
            width: 32px;
            height: 32px;
            transform: translate(-50%, -50%);
        }


        .clickableArea {
            width: 800px;
            height: 800px;
            margin: auto;
            border: 3px dashed black;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: auto;
        }
        .sidebar {
            position: fixed;
            top: 54%;
            left: 0;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f1f1f1;
            border-right: 1px solid #ccc;
            border-radius: 10px;
            padding: 2px;
            z-index: 1;
        }

        .sidebar-button {
            background-color: transparent;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }

        .sidebar-button img {
            max-width: 80%;
            max-height: 80%;
        }


        @media screen and (max-width: 600px) {
            .sidebar {
                width: 60px;
            }
            .sidebar button {
                width: 30px;
                height: 30px;
            }
        }

        #statusDropdown{
            width: 200px;
            height: 40px;
            color: black;
        }

        .assessmentInfo{
            color: #002f6c;
            font-size: 20px;
            font-weight: bold;
            flex: 1;
        }

        .formInfo{
            color: black;
        }

        .info-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: nowrap;
            width: 100%;
            gap: 50px;
        }

        .assessment-info, .icon-legend {
            flex-shrink: 0;
            flex-grow: 0;
            margin: 0;
            padding: 0;
        }

        .icon-legend img {
            width: 480px;
            height: 250px;
        }

        /* Optionally, for mobile responsiveness */
        @media screen and (max-width: 600px) {
            .info-container {
                flex-direction: column;
                align-items: center;
            }

            .assessment-info, .icon-legend {
                width: 100%;
            }

            .icon-legend img {
                width: 100%;
                max-width: 480px;
            }
}
    </style>
</head>
<?php include "navbar.php"; ?>

<body style="margin-top: 8%;">

<h1 style="font-size:250%">House Assessment Tool</h1>
<div class="info-container">
    <div class="assessment-info">
        <h2 class="assessmentInfo">Assessment ID:
            <span class="formInfo">
                <?php echo htmlspecialchars($id); ?>
            </span>
        </h2>
        <h2 class="assessmentInfo">Homeowner:
            <span class="formInfo">
                <?php echo htmlspecialchars($name); ?>
            </span>
        </h2>
        <h2 class="assessmentInfo">Address:
            <span class="formInfo">
                <?php echo htmlspecialchars($address); ?>
            </span>
        </h2>
        <label class="assessmentInfo" for="statusDropdown"><b>Current Status:</b></label>
        <select id="statusDropdown" data-id="<?php echo $id; ?>">
            <option id="statuses"value="Needs Assessment" <?php echo ($assessmentStatus === 'Needs Assessment') ? 'selected' : ''; ?>>Needs Assessment</option>
            <option id="statuses"value="Needs Bidding" <?php echo ($assessmentStatus === 'Needs Bidding') ? 'selected' : ''; ?>>Needs Bidding</option>
            <option id="statuses"value="Archived" <?php echo ($assessmentStatus === 'Archived') ? 'selected' : ''; ?>>Archive</option>
        </select>
    </div>

    <div class="icon-legend">
        <img src="images/icon-legend.png" alt="Icon Legend">
    </div>
</div>
<br>

<!-- Blueprint Image with Icons -->
<div class="clickableArea">
    <?php if (isset($layout_path)) : ?>
        <div class="assessmentArea">
            <style>
                .assessmentArea {
                    position: relative;  /* Required to contain absolute children */
                    display: inline-block;
                    width: 100%;         /* Adjust to your layout */
                    height: 100%;        /* Adjust to your layout */
                    overflow: hidden;    /* Ensures icons do not overflow */
                }
            </style>
            <img src="<?php echo $layout_path; ?>" alt="Home Layout" id="testBlueprint" style="width: 800px; height: 800px">
            <style>
                .clickableArea img {
                    max-width: 100%;
                    max-height: 100%;
                    object-fit: absolute; /* Ensures image resizes while keeping proportions */
                }
            </style>
        </div>
    <?php endif; ?>
</div>

<!-- Sidebar with Buttons -->
<div class="sidebar">
    <button id="clearButton"></button>
    <button id="select-button"></button>
    <button id="alert-severe-button"></button>
</div>

<!-- Pass Icons Data to JavaScript -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    fetch('./php_scripts/load_icons.php?id=' + assignmentID)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                localStorage.setItem("iconData", JSON.stringify(data.data));
            }
        })
        .catch(error => console.error("Error loading icons:", error));
});


$(document).ready(function () {
    $("#statusDropdown").on("change", function () {
        let newStatus = $(this).val();
        let assessmentID = $(this).data("id");

        $.ajax({
            url: "php_scripts/update_status.php",
            type: "POST",
            data: { id: assessmentID, assessmentStatus: newStatus },
            success: function (response) {
                if (response.trim() === "success") {
                    alert("Status updated successfully!");
                } else {
                    alert("Error updating status.");
                }
            }
        });
    });
});



</script>

<script>
    // Define our page as variable and pass to script.js
    const currentPage = 'assessment_tool';
</script>

</body>
</html>
