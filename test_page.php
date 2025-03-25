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
</script>

<script>
    // Define our page as variable and pass to script.js
    const currentPage = 'assessment_tool';
</script>

</body>
</html>
