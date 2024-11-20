<?php
require_once "./php_scripts/db.php";

if (isset($_POST['ajax'])) {
    $query = "SELECT * FROM form_entries";
    $result = mysqli_query($conn, $query);
    
    while ($row = mysqli_fetch_assoc($result)) {
         ?>
        
        <div class="assessment-card">
            <div class="card-header">
                <h2>Assessment: <?php echo $row['id']; ?></h2>
                
            </div>
            
            <div class="card-content">
                <div><strong>First Name:</strong> <span><?php echo $row['firstname']; ?></span></div>
                <div><strong>Last Name:</strong> <span><?php echo $row['lastname']; ?></span></div>
                <div><strong>Email:</strong> <span><?php echo $row['email']; ?></span></div>
                <div><strong>Phone:</strong> <span><?php echo $row['phone']; ?></span></div>
                <div><strong>Address:</strong> <span><?php echo $row['address']; ?></span></div>
                <div><strong>City:</strong> <span><?php echo $row['city']; ?></span></div>
                <div><strong>State:</strong> <span><?php echo $row['state']; ?></span></div>
                <div><strong>Zip:</strong> <span><?php echo $row['zip']; ?></span></div>
            </div>
            <a href="./test_page.php?id=<?=$row['id']?>" class="edit-icon">✎ Edit</a>
        </div>

        <?php
    }
    exit;
}
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="jquery-ui.css">
    <link rel="stylesheet" href="styles/indexStyle.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="jquery-ui.css"></script>
    <script src="script.js"></script>

    <style>
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }

        .content-container {
            background: white;
            padding: 20px;
            margin: 20px auto;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 70%;
            width: 100%; 
            max-height: calc(70vh - 0px);
            overflow-y: auto;
        }

        .assessment-card {
            background: #fdfdfd;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            width: 100%;
        }

        .assessment-card h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #555;
        }

        .assessment-card p {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #333;
            word-break: break-word;
        }

        .edit-icon {
            text-decoration: none;
            font-size: 16px;
            color: #007bff;
            font-weight: bold;
            float: right;
            margin-top: -10px;
        }

        .edit-icon:hover {
            text-decoration: underline;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .empty-state p {
            margin: 10px 0;
            font-size: 16px;
        }

        
        @media (max-width: 1200px) {
            .content-container{
                max-width: 100%;
            }
            .assessment-card {
                grid-template-columns: 1fr;
            }
        }
    </style>


</head>
<?php include "navbar.php"; ?>
<body>
    
    <div class="content-container">
    <h1>Existing House Assessments</h1>
        <div id="emptyState" class="empty-state">
            <p>Loading data...</p>
        </div>
        
        <div id="results"></div>
    </div>

    <script>
    $(document).ready(function() {
        function loadResults() {
            $.ajax({
                url: window.location.href,
                method: 'POST',
                data: { ajax: true },
                success: function(response) {
                    if (response.trim().length > 0) {
                        $('#results').html(response);
                        $('#emptyState').hide();
                    } else {
                        $('#results').empty();
                        $('#emptyState').show();
                    }
                }
            });
        }
        
        loadResults();
    });
    </script>
</body>
</html>
