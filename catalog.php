<?php
    include "./php_scripts/session.php";
    require_once "./php_scripts/db.php";

    if(isset($_POST['ajax'])) {
        $teamMember = isset($_POST['teamMember']) ? mysqli_real_escape_string($conn, $_POST['teamMember']) : '';
        $address = isset($_POST['address']) ? mysqli_real_escape_string($conn, $_POST['address']) : '';

        $query = "SELECT * FROM form_entries WHERE 1=1";

        if (strlen($teamMember) >= 2) {
            $query .= " AND (CONCAT(firstname, ' ', lastname) LIKE '%$teamMember%'
                         OR firstname LIKE '%$teamMember%'
                         OR lastname LIKE '%$teamMember%')";
        }

        if (strlen($address) >= 2) {
            $query .= " AND address LIKE '%$address%'";
        }

        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="assessment-card">
            <a href="javascript:void(0);" class="edit-icon" id="delete-icon" data-id="<?=$row['id']?>"><div title="Delete Assessment">🗑️</div></a>
            <a href="./test_page.php?id=<?=$row['id']?>" class="edit-icon" id="edit-icon"><div title="Edit Assessment">✎</div></a>
            <a href="php_scripts/print_to_pdf.php?id=<?=$row['id']?>" class="edit-icon" id="print-icon" target="_blank"><div title="Print PDF">📄</div></a>
                <h3>Assessment ID</h3>
                <p class="assignment-id"><?php echo $row['id']; ?></p>

                <h3>Team Member</h3>
                <p class="team-member"><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></p>

                <h3>Address</h3>
                <p class="address"><?php echo $row['address']; ?></p>


                <h3>Status</h3>
                <p>Needs Assessment</p>

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
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/catalog.css">
    <link rel="stylesheet" href="styles/indexStyle.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!--<script src="jquery-ui.css"></script>-->
    <link rel="icon" type="image/x-icon" href="/hfh-capstone/images/favicon.ico">
    <title>Assessment Catalog</title>
    

</head>
<?php include "navbar.php" ?>
<body>

    <div class="search-container">
        <h1>House Assessment Search<!--<div class="info-icon" title="User Manual">
        <img src = "images/info-circle.svg"></img></a></div>--></h1>

        <div class="search-field-group">
            <label>Team Member</label>
            <input type="text" id="teamMemberSearch" class="search-field" placeholder="Search by team member">
            <div class="min-search-notice">Enter at least 2 characters to search</div>
        </div>

        <div class="search-field-group">
            <label>Address</label>
            <input type="text" id="addressSearch" class="search-field" placeholder="Search by address">
            <div class="min-search-notice">Enter at least 2 characters to search</div>
        </div>

        <div class="search-field-group">
            <label>Status</label>
            <select class="search-field">
                <option value="in-progress" selected>Needs Assessment</option>
                <option value="needs-bidding" selected>Needs Bidding</option>
                <option value="all-statuses">All Statuses</option>
            </select>
        </div>
        <a href="about_project.php">First time using this tool? Click here for help.</a>
    </div>

    <div class="content-container">
        <div id="emptyState" class="empty-state">
            <p>Enter a team member name or address to search</p>
            <p>🔍</p>
        </div>

        <div id="noResults" class="empty-state" style="display: none;">
            <p>No matching results found</p>
            <p>Try adjusting your search terms</p>
        </div>

        <div id="results"></div>
    </div>

    <script>
    $(document).ready(function() {
        const MIN_SEARCH_LENGTH = 2;

        function updateResults() {
            var teamMember = $('#teamMemberSearch').val();
            var address = $('#addressSearch').val();

            if (teamMember.length < MIN_SEARCH_LENGTH && address.length < MIN_SEARCH_LENGTH) {
                $('#results').empty();
                $('#emptyState').show();
                $('#noResults').hide();
                return;
            }

            $.ajax({
                url: window.location.href,
                method: 'POST',
                data: {
                    ajax: true,
                    teamMember: teamMember,
                    address: address
                },
                success: function(response) {
                    $('#emptyState').hide();

                    if (response.trim().length > 0) {
                        $('#results').html(response);
                        $('#noResults').hide();
                    } else {
                        $('#results').empty();
                        $('#noResults').show();
                    }
                }
            });
        }

        function debounce(func, wait) {
            let timeout;
            return function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, arguments), wait);
            };
        }

        $('#teamMemberSearch, #addressSearch').on('input', debounce(updateResults, 300));


        $(document).on("click", ".edit-icon[data-id]", function() {
            let entryId = $(this).data("id");
            if(confirm("Are you sure you want to delete this assessment?")){
                $.ajax({
                    url: "php_scripts/delete_form.php",
                    type: "POST",
                    data: { id: entryId },
                    success: function(response){
                        if(response.trim() === "success"){
                            alert("Assessment deleted successfully.");
                            updateResults();
                        } else{
                            alert("Error deleting assessment.");
                        }
                    }
                });
            }
        });


    });



    </script>
</body>
</html>
