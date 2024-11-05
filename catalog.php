<!DOCTYPE html>
<?php
    include "./php_scripts/session.php";
    require_once "./php_scripts/db.php";
    $query = "SELECT firstname, lastname, address, city, id FROM form_entries";
    $result = mysqli_query($conn, $query);
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="jquery-ui.css">
    <link rel="stylesheet" href="styles/indexStyle.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="jquery-ui.css"></script>
    <script src="script.js"></script>
    <title>Assessment Catalog</title>

</head>
<?php include "navbar.php" ?>
<body>
    <table>
        <tr>
            <td style='color: white;'>First Name</td>
            <td style='color: white;'>Last Name</td>
            <td style='color: white;'>Address</td>
            <td style='color: white;'>City</td>
        </tr>
        <tr>
            <?php
                while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <td style='color: white;'><?php echo $row['firstname']; ?></td>
            <td style='color: white;'><?php echo $row['lastname']; ?></td>
            <td style='color: white;'><?php echo $row['address']; ?></td>
            <td style='color: white;'><?php echo $row['city']; ?></td>
            <td style='color: white;'><a href="./test_page.php?id=<?=$row['id']?>">Edit</a></td>
        </tr>
        <?php
        }
        ?>
    </table>
</body>
</html>
