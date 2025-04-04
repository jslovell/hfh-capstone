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
        #electrical-button {
            background-image: url("images/electrical.png");
        }
        #plumbing-button {
            background-image: url("images/plumbing.png");
        }
        #hvac-button {
            background-image: url("images/HVAC.png");
        }
        #door-button {
            background-image: url("images/door.png");
        }
        #stairs-button {
            background-image: url("images/stairs-button.png");
        }
        #window-button {
            background-image: url("images/window.png");
        }
        #tree-button {
            background-image: url("images/tree.png");
        }
        #deck-button {
            background-image: url("images/deck.jpg");
        }
        
        #clearButton, #select-button,#deck-button,#tree-button,#window-button,#stairs-button,#door-button,#hvac-button,#plumbing-button, #alert-severe-button,#electrical-button {
            background-repeat: no-repeat;
            background-size: contain;
            width: 58px;
            height: 58px;
            background-color: inherit;
        }


        .alert-severe-icon {
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
        
        
        .high-priority-electrical-icon, .medium-priority-electrical-icon, .low-priority-electrical-icon, .alert-severe-icon, .low-priority-deck-icon, .medium-priority-deck-icon, .high-priority-deck-icon, 
        .low-priority-tree-icon, .medium-priority-tree-icon, .high-priority-tree-icon, 
        .low-priority-window-icon, .medium-priority-window-icon, .high-priority-window-icon, 
        .low-priority-plumbing-icon, .medium-priority-plumbing-icon, .high-priority-plumbing-icon, 
        .low-priority-hvac-icon, .medium-priority-hvac-icon, .high-priority-hvac-icon, 
        .low-priority-door-icon, .medium-priority-door-icon, .high-priority-door-icon, 
        .low-priority-stairs-icon, .medium-priority-stairs-icon, .high-priority-stairs-icon {
            position: relative;
            cursor: pointer;
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            width: 32px;
            height: 32px;
            pointer-events: none;
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
            overflow: hidden;
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
<br>
<img src="images/icon-legend.png" style="width: 500px; height: 250px; justify-content: center; display: flex; align-items: center;margin: auto">
<br>
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
    <button id="clearButton"></button>
    <button id="select-button"></button>
    <button id="alert-severe-button"></button>
    <button id="electrical-button" class="sidebar-icon">
        <div class="popup-menu" id="electrical-popup">
            <div class="popup-icon low-priority-electrical-icon"></div>
            <div class="popup-icon medium-priority-electrical-icon"></div>
            <div class="popup-icon high-priority-electrical-icon"></div>
        </div>
    </button>
    <button id="plumbing-button" class="sidebar-icon">
        <div class="popup-menu" id="plumbing-popup">
            <div class="popup-icon low-priority-plumbing-icon"></div>
            <div class="popup-icon medium-priority-plumbing-icon"></div>
            <div class="popup-icon high-priority-plumbing-icon"></div>
        </div>
    </button>
    <button id="hvac-button" class="sidebar-icon">
        <div class="popup-menu" id="hvac-popup">
            <div class="popup-icon low-priority-hvac-icon"></div>
            <div class="popup-icon medium-priority-hvac-icon"></div>
            <div class="popup-icon high-priority-hvac-icon"></div>
        </div>
    </button>
    <button id="door-button" class="sidebar-icon">
        <div class="popup-menu" id="door-popup">
            <div class="popup-icon low-priority-door-icon"></div>
            <div class="popup-icon medium-priority-door-icon"></div>
            <div class="popup-icon high-priority-door-icon"></div>
        </div>
    </button>
    <button id="stairs-button" class="sidebar-icon">
        <div class="popup-menu" id="stairs-popup">
            <div class="popup-icon low-priority-stairs-icon"></div>
            <div class="popup-icon medium-priority-stairs-icon"></div>
            <div class="popup-icon high-priority-stairs-icon"></div>
        </div>
    </button>
    <button id="window-button" class="sidebar-icon">
        <div class="popup-menu" id="window-popup">
            <div class="popup-icon low-priority-window-icon"></div>
            <div class="popup-icon medium-priority-window-icon"></div>
            <div class="popup-icon high-priority-window-icon"></div>
        </div>
    </button>
    <button id="deck-button" class="sidebar-icon">
        <div class="popup-menu" id="deck-popup">
            <div class="popup-icon low-priority-deck-icon"></div>
            <div class="popup-icon medium-priority-deck-icon"></div>
            <div class="popup-icon high-priority-deck-icon"></div>
        </div>
    </button>
    <button id="tree-button" class="sidebar-icon">
        <div class="popup-menu" id="tree-popup">
            <div class="popup-icon low-priority-tree-icon"></div>
            <div class="popup-icon medium-priority-tree-icon"></div>
            <div class="popup-icon high-priority-tree-icon"></div>
        </div>
    </button>
</div>

<script>
$(document).ready(function() {

    $(".sidebar-icon").on("click", function(e) {
        
        const popup = $(this).find('.popup-menu');
        
        if (!popup.length) return;

        $(".popup-menu.visible").not(popup).removeClass("visible");

        popup.toggleClass("visible");

        const $button = $(this);
        const buttonPosition = $button.position();
        popup.css("top", buttonPosition.top + "px");


        e.stopPropagation();
    });

    $(document).on("click", function(e) {
        if (!$(e.target).closest('.popup-menu, .sidebar-icon').length) {
            $(".popup-menu.visible").removeClass("visible");
        }
    });

    $(".popup-icon").on("click", function() {
        const classes = $(this).attr("class").split(" ");
        let priority = null;
        let category = null;
        
        classes.forEach(className => {
            if (className.includes("-priority-") && className.includes("-icon")) {
                const parts = className.split("-priority-");
                priority = parts[0]; 
                category = parts[1].replace("-icon", "");
            }
        });
        
        if (category && priority) {
            const typeString = `${category}-${priority}`;
            console.log(`Selected icon type: ${typeString}`);
            
            // Set active button ID to "place"
            activeButtonId = "place";
            
            // Store selected type for later use
            $(this).closest(".sidebar-icon").data("selectedType", typeString);
            
            // Close popup
            $(this).closest(".popup-menu").hide();
        }
    });
});
</script>

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
</script>

<script>
    // Define our page as variable and pass to script.js
    const currentPage = 'assessment_tool';
</script>

</body>
</html>
