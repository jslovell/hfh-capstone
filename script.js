$(document).ready(function () {
    var activeButtonId = null;
    var isBrushActive = false;
    var isSelectMode = false;

    class Icon {
        constructor(iconId, alertType, photoData, notesData, x_pos, y_pos) {
            this.iconId = iconId;
            this.assignmentID = assignmentID;
            this.type = alertType;
            this.photo = photoData;
            this.notes = notesData;
            this.x_pos = x_pos
            this.y_pos = y_pos
        }
    }

    function fetchAndPlaceIcons() {
        if (typeof assignmentID === "undefined" || assignmentID === null) {
            console.error("Error: assignmentID is undefined or null!");
            return;
        }
    
        console.log("Fetching icons from database for assignmentID:", assignmentID);
    
        $.ajax({
            url: './php_scripts/load_icons.php',
            method: 'GET',
            data: { id: assignmentID },
            dataType: 'json',
            success: function (response, textStatus, xhr) {
                console.log("HTTP Status:", xhr.status);
                console.log("Raw Response:", xhr.responseText);
    
                try {
                    response = JSON.parse(xhr.responseText);
                } catch (e) {
                    console.error("Error parsing JSON:", e);
                    return;
                }
    
                if (response.success && response.data.length > 0) {
                    console.log("Icons data received:", response.data);
    
                    localStorage.setItem('iconData', JSON.stringify(response.data));
    
                    response.data.forEach(icon => {
                        placeIcon(icon);
                    });
                } else {
                    console.warn("No icons found or error loading icons:", response.error || "Invalid response format");
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Request Failed:");
                console.error("Status Code:", xhr.status);
                console.error("Status Text:", status);
                console.error("Error:", error);
                console.error("Response Text:", xhr.responseText);
            }
        });
    }
    
    // Ensure icons load on page load
    $(document).ready(function () {
        setTimeout(function () {
            console.log("Checking assignmentID before fetching icons:", assignmentID);
    
            if (typeof assignmentID !== "undefined" && assignmentID !== null) {
                console.log("Fetching icons for assignmentID:", assignmentID);
                fetchAndPlaceIcons();
            } else {
                console.error("assignmentID is not defined or is null!");
            }
        }, 100);
    });       

    function placeIcon(icon) {
        console.log("Placing icon:", icon);
    
        const { iconId, type, picture, notes, x_pos, y_pos } = icon;
    
        if (!iconId) {
            console.error("Error: Attempted to place an icon without an iconId!");
            return;
        }
    
        let $iconDiv = $("<div class='box-alert'><div class='alerts-icon'><img></div></div>");
        $iconDiv.attr("iconId", iconId); // Ensure iconId is set
    
        const $img = $iconDiv.find("img");
    
        if (picture) {
            $img.attr("src", "uploads/" + picture);
        } else {
            $img.attr("src", "images/default-icon.png");
        }
    
        $iconDiv.data("iconInstance", icon);
        var $assessmentArea = $(".assessmentArea");
    
        if ($assessmentArea.length === 0) {
            console.error("Error: .assessmentArea not found!");
            return;
        }
    
        console.log("Appending icon:", iconId, "to", $assessmentArea);
        $assessmentArea.append($iconDiv);
    
        // Apply percentage-based positioning
        $iconDiv.css({
            position: "absolute",
            left: x_pos + "%",
            top: y_pos + "%"
        });
    
        console.log(`Icon ${iconId} placed at (${x_pos}%, ${y_pos}%)`);
    
        // Save icon to localStorage to ensure it persists
        saveIconData(icon);
    }    
    
    function saveIconToDatabase(icon) {
        if (!icon || !icon.iconId) {
            console.error('Icon or icon ID is missing.');
            return;
        }
    
        const iconData = {
            iconId: icon.iconId,
            assignmentID: assignmentID,
            type: icon.type,
            photo: icon.photo || "", // Handle potential null/undefined
            notes: icon.notes || "",
            x_pos: icon.x_pos,
            y_pos: icon.y_pos,
        };
    
        console.log("Sending icon data to save:", iconData);
    
        $.ajax({
            url: './php_scripts/save_icon.php',
            method: 'POST',
            data: JSON.stringify(iconData),
            contentType: 'application/json',
            success: function (response) {
                console.log('Save successful:', response);
            },
            error: function (xhr, status, error) {
                console.error('Error saving icon:', error, xhr.responseText);
            }
        });
    }
    
    function saveIconData(icon) {
        if (!icon || !icon.iconId) {
            console.error("Error: Attempted to save an icon without an iconId!", icon);
            return;
        }
    
        const icons = JSON.parse(localStorage.getItem('iconData')) || [];
        const index = icons.findIndex(item => item.iconId === icon.iconId);
    
        if (index > -1) {
            icons[index] = icon; // Update existing icon
        } else {
            icons.push(icon); // Add new icon
        }
    
        localStorage.setItem('iconData', JSON.stringify(icons));
        console.log("Saved icon data:", icons);
    }    
    
    function loadIconData(iconId) {
        if (!iconId) {
            console.error("Error: Tried to load icon data with an undefined iconId!");
            return null;
        }
    
        const icons = JSON.parse(localStorage.getItem('iconData')) || [];
        const icon = icons.find(icon => icon.iconId === iconId) || null;
    
        if (!icon) {
            console.warn(`Warning: Icon with ID ${iconId} not found in localStorage.`);
        }
    
        return icon;
    }      

    function deleteIconFromDatabase(iconId) {
        if (!iconId) {
            console.error("Error: Attempted to delete an icon without an iconId!");
            return;
        }
    
        console.log(`Deleting icon with ID: ${iconId}`);
    
        $.ajax({
            url: './php_scripts/delete_icon.php',
            method: 'POST',
            data: JSON.stringify({ iconId: iconId }), // Ensure it's correctly sent as JSON
            contentType: 'application/json',
            success: function (response) {
                console.log("Delete response:", response);
    
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    console.error("Error parsing JSON response from delete:", e);
                    return;
                }
    
                if (response.success) {
                    console.log(`Successfully deleted icon ${iconId} from the database.`);
    
                    // Remove from localStorage
                    let icons = JSON.parse(localStorage.getItem('iconData')) || [];
                    icons = icons.filter(icon => icon.iconId !== iconId);
                    localStorage.setItem('iconData', JSON.stringify(icons));
    
                    // Remove from the DOM
                    $(`[iconId="${iconId}"]`).remove();
                    console.log(`Icon ${iconId} removed from screen.`);
                } else {
                    console.error(`Error deleting icon ${iconId}:`, response.error);
                }
            },
            error: function (xhr, status, error) {
                console.error(`Error deleting icon ${iconId}:`, error, xhr.responseText);
            }
        });
    }    
    
    // Currently unused, to be used for trash can icon in future sidebar
    function deleteAllIconsFromDatabase() {
        $.ajax({
            url: './php_scripts/delete_all_icons.php',
            method: 'POST',
            data: JSON.stringify({ assignmentID }),
            contentType: 'application/json',
            success: function (response) {
                if (response.success) {
                    console.log('All icons deleted successfully:', response.message);
                } else {
                    console.error('Error deleting all icons:', response.error);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX error deleting all icons:', error, xhr.responseText);
            }
        });
    }    

    $(document).on("click", ".alerts-icon", function (e) {
        e.preventDefault();
        var $iconDiv = $(this).closest('.box-alert');
        var iconId = $iconDiv.attr("iconId");
    
        if (!iconId) {
            console.error("Error: Clicked an icon but it has no valid iconId.");
            return;
        }
    
        console.log(`Icon clicked, opening edit popup for ID: ${iconId}`);
        openEditPopup(iconId);
    });
    
    function openEditPopup(iconId) {
        console.log(`Attempting to open edit popup for icon ID: ${iconId}`);
    
        if (!iconId) {
            console.error("Error: openEditPopup was called with an undefined iconId.");
            return;
        }
    
        const $iconElement = $(`[iconId="${iconId}"]`);
        let iconInstance = $iconElement.data("iconInstance");
    
        if (!iconInstance) {
            console.warn(`Icon with ID ${iconId} not found in DOM, attempting localStorage.`);
            iconInstance = loadIconData(iconId);
        }
    
        if (!iconInstance) {
            console.error(`Icon with ID ${iconId} not found in DOM or localStorage.`);
            return;
        }
    
        console.log(`Opening edit popup for icon:`, iconInstance);
    
        // Ensure only one popup exists at a time
        $(".alerts").remove();
    
        // Construct popup content
        const popupContent = `
            <div id="edit-popup">
                <label for="alert-type">Icon Type:</label>
                <select id='alert-type'>
                    <option value='1'>Window(s)</option>
                    <option value='2'>Door(s)</option>
                    <option value='3'>Siding</option>
                    <option value='4'>Porch</option>
                    <option value='5'>Stairs</option>
                    <option value='6'>Deck</option>
                    <option value='7'>Mechanical (HVAC)</option>
                    <option value='8'>Plumbing</option>
                    <option value='9'>Electrical</option>
                    <option value='10'>Flatwork</option>
                    <option value='11'>Tree Maintenance</option>
                    <option value='12'>Roofing</option>
                    <option value='other'>Other</option>
                </select><br>
                <input type='file' id='icon-photo' accept='image/*'><br>
                <label for="icon-notes">Notes:</label>
                <textarea id="icon-notes" placeholder="Enter notes">${iconInstance.notes || ''}</textarea><br>
                <button id="save-button">Save</button>
                <button id="delete-button">Delete</button>
            </div>
        `;
    
        // Append the popup to the body
        $("body").append(popupContent);
    
        // Initialize jQuery UI Dialog
        $("#edit-popup").dialog({
            width: 500,
            height: 'auto',
            modal: true,
            title: 'Edit Icon',
            close: function () {
                $(this).dialog('destroy').remove();
            }
        });
    
        // Set pre-filled data
        $("#alert-type").val(iconInstance.type || '');
    
        // Save button event
        $("#save-button").on("click", function () {
            iconInstance.type = $("#alert-type").val();
            iconInstance.notes = $("#icon-notes").val();
            saveIconToDatabase(iconInstance);
            $("#edit-popup").dialog('close');
        });
    
        // Delete button event
        $("#delete-button").on("click", function () {
            console.log(`Delete button clicked for icon ${iconId}`);

            if (!iconId) {
                console.error("Error: Delete button clicked but iconId is missing.");
                return;
            }

            deleteIconFromDatabase(iconId);

            // Close the popup immediately
            $("#edit-popup").dialog('close');
        });
    }    

    $(document).on("mousedown", ".assessmentArea", function (event) {
        if (isSelectMode && activeButtonId) {
            var iconId = "icon-" + Date.now();
    
            var $assessmentArea = $(".assessmentArea");
            var rect = $assessmentArea[0].getBoundingClientRect();
            var areaWidth = rect.width;
            var areaHeight = rect.height;
    
            // Calculate x and y relative to the assessmentArea
            var x = ((event.clientX - rect.left) / areaWidth) * 100;
            var y = ((event.clientY - rect.top) / areaHeight) * 100;
    
            // Create the new icon object
            const newIcon = new Icon(iconId, null, null, null, x, y);
    
            // Call placeIcon() which will create and append the icon properly
            placeIcon(newIcon);
    
            // Save the icon
            saveIconData(newIcon);
            saveIconToDatabase(newIcon);
        }
    });    

    // Navbar and side navigation functionality
    function toggleSideNav() {
        $(".side_nav,.nav-overlay").toggleClass("active");
    }

    $(window).on("resize", function () {
        console.log("Window resized, recalculating icon positions...");
    
        const $assessmentArea = $(".assessmentArea");
        const $assessmentImg = $("#assessment-img");
    
        if (!$assessmentImg.length) {
            console.warn("Assessment image not found in the DOM!");
            return;
        }
    
        // Get the new size of the assessment image
        const newWidth = $assessmentImg.width();
        const newHeight = $assessmentImg.height();
    
        console.log("New assessment image size:", newWidth, newHeight);
    
        // Get stored icons
        const icons = JSON.parse(localStorage.getItem('iconData')) || [];
    
        // Clear the assessment area before placing icons again
        $assessmentArea.empty();
    
        // Re-insert the assessment image
        $assessmentArea.append($assessmentImg);
    
        // Recalculate and reposition icons
        icons.forEach(icon => {
            const adjustedX = (icon.x_pos / 100) * newWidth;
            const adjustedY = (icon.y_pos / 100) * newHeight;
    
            console.log(`Repositioning icon ${icon.iconId} to (${adjustedX}px, ${adjustedY}px)`);
    
            let $iconDiv = $(`[iconId="${icon.iconId}"]`);
            if (!$iconDiv.length) {
                $iconDiv = $("<div class='box-alert'><div class='alerts-icon'><img></div></div>");
                $iconDiv.attr("iconId", icon.iconId);
                $assessmentArea.append($iconDiv);
            }
    
            $iconDiv.css({
                position: "absolute",
                left: adjustedX + "px",
                top: adjustedY + "px"
            });
        });
    });    

    $(".nav_ico").click(function () {
        $(this).toggleClass("active");
        toggleSideNav();
        return false;
    });

    $(".nav-overlay").click(function () {
        $(".nav_ico").removeClass("active");
        toggleSideNav();
    });

    $(".side_nav a[data-target]").click(function (event) {
        event.preventDefault();
        const targetId = $(this).attr('data-target');
        const targetNav = $("#" + targetId);

        if (targetNav) {
            $(".nav_ico").removeClass("active");
            toggleSideNav();
        }
    });

    function toggleBrush() {
        isBrushActive = !isBrushActive;
        $("body").toggleClass("brush-cursor", isBrushActive);
    }

    function deactivateIcons() {
        $("#alert-button").css("background-image", "url('images/alert-button.png')");
        $("#alert-moderate-button").css("background-image", "url('images/alert-moderate-button.png')");
        $("#alert-severe-button").css("background-image", "url('images/alert-severe-button.png')");
    }

    function toggleSelectMode() {
        isSelectMode = !isSelectMode;
        if (isSelectMode) {
            $("#select-button").css("background-image", "url('images/select-button-active.png')");
            toggleBrush();
        } else {
            $("#select-button").css("background-image", "url('images/select-button.png')");
            deactivateIcons();
            isBrushActive = false;
        }
    }    

    $("#select-button").click(function () {
        toggleSelectMode();
        activeButtonId = null;
    });

    $("#alert-severe-button, #alert-moderate-button, #alert-button").on("click", function () {
        if (isSelectMode) {
            activeButtonId = $(this).attr("id");
        }
    });

    $("#clearButton").on("click", function () {
        let text = "Warning: All icons will be deleted\n";
        if (confirm(text)) {
            $(".box, .box-alert, .box-note").remove();
            deactivateIcons();
            isSelectMode = false;
            isBrushActive = false;
            $("#select-button").css("background-image", "url('images/select-button.png')");
            deleteAllIconsFromDatabase();
            localStorage.clear();
        }
    });
});
