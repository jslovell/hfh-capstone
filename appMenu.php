<!DOCTYPE html>
<?php include "./php_scripts/session.php" ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="jquery-ui.css">
    <link rel="stylesheet" href="styles/indexStyle.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="jquery-ui.css"></script>
    <script src="script.js"></script>
    <title>Menu Selection</title>

</head>
<?php include "navbar.php" ?>
<body>
<form method="post">
    <div class="container">
        <button type="button" class="createAssessment" onclick="location.href='houseAssesmentTool.php'">Create New Assessment</button>
        <button type="button" class="editAssessment" onclick="location.href='test_page.php'">Edit Existing Assessment</button>
    </div>
</form>



</body>


</html>