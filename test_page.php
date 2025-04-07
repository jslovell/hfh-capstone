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
    <link rel="stylesheet" href="./styles/navbar.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="jquery-ui.js"></script>
    <link rel="icon" type="image/x-icon" href="/hfh-capstone/images/favicon.ico">
    <script>
        const assignmentID = <?php echo json_encode($assignmentID); ?>;
    </script>

    <script src="script.js"></script>
    <style>
        #clear-button {
            background-image: url("images/clear.png");
        }
        #select-button {
            background-image: url("images/select.png");
        }
        #null-button {
            background-image: url("images/null.png");
        }
        #electrical-button {
            background-image: url("images/electrical.png");
        }
        #plumbing-button {
            background-image: url("images/plumbing.png");
        }
        #hvac-button {
            background-image: url("images/hvac.png");
        }
        #door-button {
            background-image: url("images/door.png");
        }
        #stairs-button {
            background-image: url("images/stairs.png");
        }
        #window-button {
            background-image: url("images/window.png");
        }
        #tree-button {
            background-image: url("images/tree.png");
        }
        #deck-button {
            background-image: url("images/deck.png");
        }
        
        #clear-button, #null-button, #select-button,#deck-button,#tree-button,#window-button,#stairs-button,#door-button,#hvac-button,#plumbing-button, #null-button,#electrical-button {
            background-repeat: no-repeat;
            background-size: contain;
            width: 58px;
            height: 58px;
            background-color: inherit;
        }


        .null-icon {
            background-image: url("images/alert-sever-icon.png");
        }
        .low-priority-stairs-icon{
            background-image: url("images/low-priority-stairs-icon.png");
        }
        .medium-priority-stairs-icon{
            background-image: url("images/medium-priority-stairs-icon.png");
        }
        .high-priority-stairs-icon{
            background-image: url("images/high-priority-stairs-icon.png");
        }

        .low-priority-electrical-icon{
            background-image: url("images/low-priority-electrical-icon.png");
        }
        .medium-priority-electrical-icon{
            background-image: url("images/medium-priority-electrical-icon.png");
        }
        .high-priority-electrical-icon{
            background-image: url("images/high-priority-electrical-icon.png");
        }

        .low-priority-plumbing-icon{
            background-image: url("images/low-priority-plumbing-icon.png");
        }
        .medium-priority-plumbing-icon{
            background-image: url("images/medium-priority-plumbing-icon.png");
        }
        .high-priority-plumbing-icon{
            background-image: url("images/high-priority-plumbing-icon.png");
        }

        .low-priority-hvac-icon{
            background-image: url("images/low-priority-hvac-icon.png");
        }
        .medium-priority-hvac-icon{
            background-image: url("images/medium-priority-hvac-icon.png");
        }
        .high-priority-hvac-icon{
            background-image: url("images/high-priority-hvac-icon.png");
        }

        .low-priority-door-icon{
            background-image: url("images/low-priority-door-icon.png");
        }
        .medium-priority-door-icon{
            background-image: url("images/medium-priority-door-icon.png");
        }
        .high-priority-door-icon{
            background-image: url("images/high-priority-door-icon.png");
        }

        .low-priority-window-icon{
            background-image: url("images/low-priority-window-icon.png");
        }
        .medium-priority-window-icon{
            background-image: url("images/medium-priority-window-icon.png");
        }
        .high-priority-window-icon{
            background-image: url("images/high-priority-window-icon.png");
        }

        .low-priority-tree-icon{
            background-image: url("images/low-priority-tree-icon.png");
        }
        .medium-priority-tree-icon{
            background-image: url("images/medium-priority-tree-icon.png");
        }
        .high-priority-tree-icon{
            background-image: url("images/high-priority-tree-icon.png");
        }

        .low-priority-deck-icon{
            background-image: url("images/low-priority-deck-icon.png");
        }
        .medium-priority-deck-icon{
            background-image: url("images/medium-priority-deck-icon.png");
        }
        .high-priority-deck-icon{
            background-image: url("images/high-priority-deck-icon.png");
        }

        .low-priority-null-icon{
            background-image: url("images/low-priority-null-icon.png");
        }

        .medium-priority-null-icon{
            background-image: url("images/medium-priority-null-icon.png");
        }

        .high-priority-null-icon{
            background-image: url("images/high-priority-null-icon.png");
        }


        
        
        .high-priority-electrical-icon, .medium-priority-electrical-icon, .low-priority-electrical-icon, .null-icon, .low-priority-deck-icon, .medium-priority-deck-icon, .high-priority-deck-icon, 
        .low-priority-tree-icon, .medium-priority-tree-icon, .high-priority-tree-icon, 
        .low-priority-window-icon, .medium-priority-window-icon, .high-priority-window-icon, 
        .low-priority-plumbing-icon, .medium-priority-plumbing-icon, .high-priority-plumbing-icon, 
        .low-priority-hvac-icon, .medium-priority-hvac-icon, .high-priority-hvac-icon, 
        .low-priority-door-icon, .medium-priority-door-icon, .high-priority-door-icon, 
        .low-priority-stairs-icon, .medium-priority-stairs-icon, .high-priority-stairs-icon, .low-priority-null-icon,
        .medium-priority-null-icon, .high-priority-null-icon {
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
            flex-wrap: wrap;
            width: 120px;
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
        .popup-menu {
            position: absolute;
            left: 140px;
            background-color:rgb(255, 255, 255);
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 8px;
            display: flex;
            gap: 10px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s;
            z-index: 1000;
        }
        
        .popup-menu.visible {
            opacity: 1;
            visibility: visible;
        }
        
        .popup-icon {
            width: 32px;
            height: 32px;
            cursor: pointer;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        
        .popup-menu::before {
            content: "";
            position: absolute;
            top: 50%;
            left: -10px;
            transform: translateY(-50%);
            border-width: 5px;
            border-style: solid;
            border-color: transparent #ddd transparent transparent;
        }

        .popup-menu .popup-icon {
            pointer-events: auto;
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
                    position: relative;
                    display: inline-block;
                    width: 100%;        
                    height: 100%;       
                    overflow: hidden;   
                }   
            </style>
            <img src="<?php echo $layout_path; ?>" alt="Home Layout" id="testBlueprint" style="width: 800px; height: 800px">
            <style>
                .clickableArea img {
                    max-width: 100%;
                    max-height: 100%;
                    object-fit: absolute;
                }
            </style>
        </div>
    <?php endif; ?>
</div>

<!-- Sidebar with Buttons -->
<div class="sidebar">
    <button id="clear-button" ></button>
    <button id="select-button" ></button>
    <button id="null-button" class="sidebar-icon" >
        <div class="popup-menu" id="null-popup">
            <div class="popup-icon low-priority-null-icon"></div>
            <div class="popup-icon medium-priority-null-icon"></div>
            <div class="popup-icon high-priority-null-icon"></div>
        </div>
    </button>
    <button id="electrical-button" class="sidebar-icon" >
        <div class="popup-menu" id="electrical-popup">
            <div class="popup-icon low-priority-electrical-icon"></div>
            <div class="popup-icon medium-priority-electrical-icon"></div>
            <div class="popup-icon high-priority-electrical-icon"></div>
        </div>
    </button>
    <button id="plumbing-button" class="sidebar-icon" >
        <div class="popup-menu" id="plumbing-popup">
            <div class="popup-icon low-priority-plumbing-icon"></div>
            <div class="popup-icon medium-priority-plumbing-icon"></div>
            <div class="popup-icon high-priority-plumbing-icon"></div>
        </div>
    </button>
    <button id="hvac-button" class="sidebar-icon" >
        <div class="popup-menu" id="hvac-popup">
            <div class="popup-icon low-priority-hvac-icon"></div>
            <div class="popup-icon medium-priority-hvac-icon"></div>
            <div class="popup-icon high-priority-hvac-icon"></div>
        </div>
    </button>
    <button id="door-button" class="sidebar-icon" >
        <div class="popup-menu" id="door-popup">
            <div class="popup-icon low-priority-door-icon"></div>
            <div class="popup-icon medium-priority-door-icon"></div>
            <div class="popup-icon high-priority-door-icon"></div>
        </div>
    </button>
    <button id="stairs-button" class="sidebar-icon" >
        <div class="popup-menu" id="stairs-popup">
            <div class="popup-icon low-priority-stairs-icon"></div>
            <div class="popup-icon medium-priority-stairs-icon"></div>
            <div class="popup-icon high-priority-stairs-icon"></div>
        </div>
    </button>
    <button id="window-button" class="sidebar-icon" >
        <div class="popup-menu" id="window-popup">
            <div class="popup-icon low-priority-window-icon"></div>
            <div class="popup-icon medium-priority-window-icon"></div>
            <div class="popup-icon high-priority-window-icon"></div>
        </div>
    </button>
    <button id="deck-button" class="sidebar-icon" >
        <div class="popup-menu" id="deck-popup">
            <div class="popup-icon low-priority-deck-icon"></div>
            <div class="popup-icon medium-priority-deck-icon"></div>
            <div class="popup-icon high-priority-deck-icon"></div>
        </div>
    </button>
    <button id="tree-button" class="sidebar-icon" >
        <div class="popup-menu" id="tree-popup">
            <div class="popup-icon low-priority-tree-icon"></div>
            <div class="popup-icon medium-priority-tree-icon"></div>
            <div class="popup-icon high-priority-tree-icon"></div>
        </div>
    </button>
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
