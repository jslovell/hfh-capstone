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
    <link rel="stylesheet" href="styles/navbar.css">
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
        #photo-button {
            background-image: url("images/picture-button.png");
        }
        #note-button {
            background-image: url("images/note-button.png");
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
        #low-priority-icon {
            background-image: url("images/alert-severe-button.png");
        }
        #medium-priority-icon {
            background-image: url("images/alert-severe-button.png");
        }
        #high-priority-icon {
            background-image: url("images/alert-severe-button.png");
        }
        #alert-severe-button{
            background-image: url("images/alert-severe-button.png")
        }
            /*
        #alert-moderate-button {
            background-image: url("images/alert-moderate-button.png");
        }
        #alert-button {
            background-image: url("images/alert-button.png");
        }
        
            */
        #clearButton,#alert-severe-button, #select-button, #photo-button, #electrical-button, #deck-button, #tree-button, #removal-button, #note-button, #window-button, #door-button, #hvac-button, #plumbing-button,#electrical-button, #stairs-button {
            background-repeat: no-repeat;
            background-size: contain;
            width: 58px;
            height: 58px;
            background-color: inherit;
        }

        
        .alert-severe-icon {
            background-image: url("images/alert-sever-icon.png");
        }
        /*
        .alert-moderate-icon {
            background-image: url("images/alert-moderate-icon.png");
        }
        .alert-icon {
            background-image: url("images/alert-icon.png");
        }

        
        .picture-icon {
            background-image: url("images/picture-icon.png");
        }
        .note-icon {
            background-image: url("images/note-icon.png");
        }

        */

        .alert-icon, .alert-moderate-icon, .alert-severe-icon, .note-icon, .low-priority-icon, .medium-priority-icon, .high-priority-icon {
            position: relative;
            cursor: pointer;
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            width: 32px;
            height: 32px;
            pointer-events: none;
        }
        
        #removal-button {
            background-image: url("images/removal-button.png");

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

        .popup-menu {
            position: absolute;
            left: 65px;
            background-color:rgb(0, 0, 0);
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
<!--<img src="images/icon-legend.png" style="width: 500px; height: 250px; justify-content: center; display: flex; align-items: center;margin: auto">  -->
<br>
<br>

<!-- Blueprint Image with Icons -->
<div class="clickableArea">
    <?php if (isset($layout_path)) : ?>
        <div class="assessmentArea">
            <style>
                .assessmentArea {
                position: relative;  /* Required to contain absolute children */
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

<!-- Specify later for each pop up icon to be electrical-low.... instead of just low-priority-icon -->
<div class="sidebar">
    <button id="clearButton"></button>
    <button id="select-button"></button>
    <button id="photo-button"></button>
    <button id="note-button"></button>
    <button id="alert-severe-button"></button>
    <button id="electrical-button" class="sidebar-icon">
        <div class="popup-menu" id="electrical-popup">
            <div class="popup-icon low-priority-icon"></div>
            <div class="popup-icon medium-priority-icon"></div>
            <div class="popup-icon high-priority-icon"></div>
        </div>
    </button>
    <button id="plumbing-button" class="sidebar-icon">
        <div class="popup-menu" id="plumbing-popup">
            <div class="popup-icon low-priority-icon"></div>
            <div class="popup-icon medium-priority-icon"></div>
            <div class="popup-icon high-priority-icon"></div>
        </div>
    </button>
    <button id="hvac-button" class="sidebar-icon">
        <div class="popup-menu" id="hvac-popup">
            <div class="popup-icon low-priority-icon"></div>
            <div class="popup-icon medium-priority-icon"></div>
            <div class="popup-icon high-priority-icon"></div>
        </div>
    </button>
    <button id="door-button" class="sidebar-icon">
        <div class="popup-menu" id="door-popup">
            <div class="popup-icon low-priority-icon"></div>
            <div class="popup-icon medium-priority-icon"></div>
            <div class="popup-icon high-priority-icon"></div>
        </div>
    </button>
    <button id="stairs-button" class="sidebar-icon">
        <div class="popup-menu" id="stairs-popup">
            <div class="popup-icon low-priority-icon"></div>
            <div class="popup-icon medium-priority-icon"></div>
            <div class="popup-icon high-priority-icon"></div>
        </div>
    </button>
    <button id="window-button" class="sidebar-icon">
        <div class="popup-menu" id="window-popup">
            <div class="popup-icon low-priority-icon"></div>
            <div class="popup-icon medium-priority-icon"></div>
            <div class="popup-icon high-priority-icon"></div>
        </div>
    </button>
    <button id="deck-button" class="sidebar-icon">
        <div class="popup-menu" id="deck-popup">
            <div class="popup-icon low-priority-icon"></div>
            <div class="popup-icon medium-priority-icon"></div>
            <div class="popup-icon high-priority-icon"></div>
        </div>
    </button>
    <button id="tree-button" class="sidebar-icon">
        <div class="popup-menu" id="tree-popup">
            <div class="popup-icon low-priority-icon"></div>
            <div class="popup-icon medium-priority-icon"></div>
            <div class="popup-icon high-priority-icon"></div>
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

    $(".popup-icon").on("click", function(e) {
        const priorityClass = $(this).attr("class").split(" ")[1]; 
        const parentButtonId = $(this).closest('.sidebar-icon').attr('id');
        activeButtonId = parentButtonId;

        console.log(`Clicked ${priorityClass} in ${parentButtonId}`);
        
        if (parentButtonId === "electrical-button") {
        if (priorityClass === "low-priority-icon") {
            iconTypeNumber = "9-low";
        } else if (priorityClass === "medium-priority-icon") {
            iconTypeNumber = "9-medium";
        } else if (priorityClass === "high-priority-icon") {
            iconTypeNumber = "9-high";
        }
        } else if (parentButtonId === "plumbing-button") {
            if (priorityClass === "low-priority-icon") {
                iconTypeNumber = "8-low";
            } else if (priorityClass === "medium-priority-icon") {
                iconTypeNumber = "8-medium";
            } else if (priorityClass === "high-priority-icon") {
                iconTypeNumber = "8-high";
            }
        } else if (parentButtonId === "hvac-button") {
            if (priorityClass === "low-priority-icon") {
                iconTypeNumber = "7-low";
            } else if (priorityClass === "medium-priority-icon") {
                iconTypeNumber = "7-medium";
            } else if (priorityClass === "high-priority-icon") {
                iconTypeNumber = "7-high";
            }
        } else if (parentButtonId === "door-button") {
            if (priorityClass === "low-priority-icon") {
                iconTypeNumber = "2-low";
            } else if (priorityClass === "medium-priority-icon") {
                iconTypeNumber = "2-medium";
            } else if (priorityClass === "high-priority-icon") {
                iconTypeNumber = "2-high";
            }
        } else if (parentButtonId === "stairs-button") {
            if (priorityClass === "low-priority-icon") {
                iconTypeNumber = "5-low";
            } else if (priorityClass === "medium-priority-icon") {
                iconTypeNumber = "5-medium";
            } else if (priorityClass === "high-priority-icon") {
                iconTypeNumber = "5-high";
            }
        } else if (parentButtonId === "window-button") {
            if (priorityClass === "low-priority-icon") {
                iconTypeNumber = "1-low";
            } else if (priorityClass === "medium-priority-icon") {
                iconTypeNumber = "1-medium";
            } else if (priorityClass === "high-priority-icon") {
                iconTypeNumber = "1-high";
            }
        } else if (parentButtonId === "deck-button") {
            if (priorityClass === "low-priority-icon") {
                iconTypeNumber = "6-low";
            } else if (priorityClass === "medium-priority-icon") {
                iconTypeNumber = "6-medium";
            } else if (priorityClass === "high-priority-icon") {
                iconTypeNumber = "6-high";
            }
        } else if (parentButtonId === "tree-button") {
            if (priorityClass === "low-priority-icon") {
                iconTypeNumber = "11-low";
            } else if (priorityClass === "medium-priority-icon") {
                iconTypeNumber = "11-medium";
            } else if (priorityClass === "high-priority-icon") {
                iconTypeNumber = "11-high";
            }
        } else if(parentButtonId == )

        selectedIconType = iconTypeNumber;
        $(this).closest('.popup-menu').removeClass("visible");
        e.stopPropagation();
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
