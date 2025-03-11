<?php 
include "./php_scripts/session.php"; 
require_once "./php_scripts/db.php";
include "navbar.php";

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
    <link rel="stylesheet" href="./styles/tabToolStyle.css">
    <link rel="stylesheet" href="./test_pagetempcss.css">
    <!-- <link rel="stylesheet" href="styles/navbar.css"> -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="jquery-ui.js"></script>
    <script>
        const assignmentID = <?php echo json_encode($assignmentID); ?>;
    </script>

    <script src="script.js"></script>

</head>

<body style="margin-top: 8%; background-image: url(../hfh-capstone/images/GraphBackground.png);">

<div class="popup" onclick="myFunction()">Click me!
  <span class="popuptext" id="myPopup">Popup text...</span>
</div>

<h1 style="font-size:250%">House Assessment Tool</h1>
<br>
<img src="images/icon-legend.png" style="width: 500px; height: 250px; justify-content: center; display: flex; align-items: center;margin: auto">
<br>
<br>

<!-- Blueprint Image with Icons -->
<div class="clickableArea">
    <?php if (isset($layout_path)) : ?>
        <img src="<?php echo $layout_path; ?>" alt="Home Layout" id="testBlueprint" style="width: 800px; height: 800px">
    <?php endif; ?>
</div>

<!-- Sidebar with Buttons -->
<div class="sidebar">
    <button id="clearButton"></button>
    <button id="select-button"></button>
    <button id="photo-button" onclick="display()"></button>
    <button id="note-button"></button>
    <button id="alert-severe-button"></button>
    <button id="alert-moderate-button"></button>
    <button id="alert-button"></button>
    <button id="deck-button"></button>
    <button id="door-button"></button>
    <button id="electrical-button"></button>
    <button id="HVAC-button"></button>
    <button id="plumbing-button"></button>
    <button id="stairs-button"></button>
    <button id="tree-button"></button>
    <button id="window-button"></button>    
</div>

<!-- Pass Icons Data to JavaScript -->
<script>
    const existingIcons = <?php echo json_encode($icons); ?>;
    document.addEventListener("DOMContentLoaded", function () {
        if (existingIcons.length > 0) {
            // Render existing icons dynamically on the layout
            existingIcons.forEach(icon => {
                const $icon = $("<div class='box-alert'></div>");
                $icon.attr("id", "icon-" + icon.local_idx);
                $icon.css({
                    top: icon.y_pos + "px",
                    left: icon.x_pos + "px",
                });
                $icon.html("<div class='alerts-icon'><img src='images/alert-icon.png'></div>");
                $(".clickableArea").append($icon);

                // Store icon details in localStorage
                const iconObject = {
                    id: $icon.attr("id"),
                    alertType: icon.type,
                    photoData: icon.picture,
                    notesData: icon.notes,
                };
                const iconData = JSON.parse(localStorage.getItem("iconData")) || {};
                iconData[iconObject.id] = iconObject;
                localStorage.setItem("iconData", JSON.stringify(iconData));
            });
        }
    });
</script>


<script>
    // Define our page as variable and pass to script.js
    const currentPage = 'assessment_tool';
</script>

</body>
</html>

