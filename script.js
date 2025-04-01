$(document).ready(function () {
    var activeButtonId = null;
    var activePriorityClass = null;
    var isBrushActive = false;
    var isSelectMode = false;

    class Icon {
        constructor(iconId, type, photoData, notesData, x_pos, y_pos) {
            // Generate a unique ID if one is not provided or is invalid
            this.iconId = (iconId && iconId !== "icon-undefinedundefined") 
                ? iconId 
                : "icon-" + Date.now() + "-" + Math.floor(Math.random() * 1000);
            this.assignmentID = assignmentID;
            this.type = type || "other-medium"; 
            this.photo = photoData || "";
            this.notes = notesData || "";
            this.x_pos = parseInt(x_pos) || 0;
            this.y_pos = parseInt(y_pos) || 0;
        }
    }

    function getCategoryFromType(type) {
        if (!type) return 'other';
        return type.split('-')[0];
    }

    function getPriorityFromType(type) {
        if (!type) return 'medium';
        const parts = type.split('-');
        return parts.length > 1 ? parts[1] : 'medium';
    }

    function createTypeString(category, priority) {
        return `${category || 'other'}-${priority || 'medium'}`;
    }

    function fetchAndPlaceIcons() {
        $.ajax({
            url: './php_scripts/load_icons.php',
            method: 'GET',
            data: { id: assignmentID },
            success: function (response) {
                try {
                    // Try to parse the response if it's a string
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (data && data.success && Array.isArray(data.data)) {
                        // Clear existing icons first to prevent duplicates
                        $('.box-alert').remove();
                        
                        // Save icons to localStorage with proper format
                        const iconData = {};
                        
                        // Place all icons on the page and save to localStorage
                        data.data.forEach(icon => {
                            // Skip icons with missing data
                            if (!icon || !icon.iconId) return;
                            
                            placeIcon(icon);
                            iconData[icon.iconId] = icon;
                        });
                        
                        // Save to localStorage in the expected format
                        localStorage.setItem('iconData', JSON.stringify(iconData));
                        console.log('Icons loaded successfully:', Object.keys(iconData).length);
                    } else {
                        console.log('No icons found or response format incorrect:', data);
                    }
                } catch (e) {
                    console.error('Error handling icon data:', e);
                    console.log('Raw response:', response);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error loading icons:', error);
                console.log('Server response:', xhr.responseText);
            }
        });
    }    

    function placeIcon(icon) {
        // Validate icon data
        if (!icon || !icon.iconId) {
            console.error('Invalid icon data:', icon);
            return;
        }

        const { iconId, type, picture, notes, x_pos, y_pos } = icon;

        // Ensure x_pos and y_pos are valid numbers
        const posX = parseInt(x_pos) || 0;
        const posY = parseInt(y_pos) || 0;

        // Create a new icon element
        let $iconDiv = $("<div class=\"box-alert\"><div class=\"alerts-icon\"><img></div></div>");
        $iconDiv.attr("iconId", iconId);
        $iconDiv.css({
            top: `${posY}px`,
            left: `${posX}px`,
            position: 'absolute'
        });
    
        const $img = $iconDiv.find("img");
        
        // Get category and priority from the type
        const category = getCategoryFromType(type);
        const priority = getPriorityFromType(type);
    
        // Set the icon image based on its category and priority
        let imagePath = "images/alert-icon.png";  // Default fallback
        
        // First try category-specific image
        if (category !== 'other') {
            imagePath = `images/${priority}-priority-${category}-icon.png`;
        } else {
            // Use legacy images for backward compatibility
            switch (priority) {
                case 'high':
                    imagePath = "images/alert-severe-icon.png";
                    break;
                case 'medium':
                    imagePath = "images/alert-moderate-icon.png";
                    break;
                case 'low':
                    imagePath = "images/alert-icon.png";
                    break;
            }
        }
        
        $img.attr("src", imagePath);
    
        // Store complete icon data with the DOM element
        $iconDiv.data("iconData", { 
            iconId, type, picture, notes, x_pos: posX, y_pos: posY 
        });
    
        $(".clickableArea").append($iconDiv);
        
        console.log(`Placed icon ${iconId} at position ${posX},${posY}`);
    }

    // Check if current page is the assessment tool, if so, load icons
    if (typeof currentPage !== 'undefined' && currentPage === 'assessment_tool') {
        fetchAndPlaceIcons();
    }

    function saveIconToDatabase(icon) {
        if (!icon || !icon.iconId) {
            console.error('Icon or icon ID is missing.');
            return;
        }
    
        // Ensure all properties have valid values before sending
        const iconData = {
            iconId: icon.iconId,
            assignmentID: assignmentID || 0,
            type: icon.type || 'other-medium',
            photo: icon.photo || "", 
            notes: icon.notes || "",
            x_pos: parseInt(icon.x_pos) || 0,
            y_pos: parseInt(icon.y_pos) || 0,
        };
    
        console.log("Sending icon data to save:", iconData);
    
        $.ajax({
            url: './php_scripts/save_icon.php',
            method: 'POST',
            data: JSON.stringify(iconData),
            contentType: 'application/json',
            success: function (response) {
                console.log('Save response:', response);
                
                // Don't attempt to parse if the response is an error message
                if (typeof response === 'string' && response.includes('<br />')) {
                    console.error('Server error response:', response);
                    
                    // Check if error is about duplicate entry for iconId
                    if (response.includes('Duplicate entry') && response.includes('iconId')) {
                        // If duplicate error, try updating the icon in localStorage only
                        console.log('Duplicate iconId, updating local storage only');
                        var iconData = JSON.parse(localStorage.getItem('iconData')) || {};
                        iconData[icon.iconId] = icon;
                        localStorage.setItem('iconData', JSON.stringify(iconData));
                    }
                    return;
                }
                
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    if (data && data.success) {
                        console.log('Icon saved successfully');
                    } else {
                        console.warn('Save returned unsuccessful status:', data);
                    }
                } catch (e) {
                    console.error('Error parsing save response:', e);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error saving icon:', error);
                console.log('Server response:', xhr.responseText);
                
                // Still update localStorage even if server save fails
                var iconData = JSON.parse(localStorage.getItem('iconData')) || {};
                iconData[icon.iconId] = icon;
                localStorage.setItem('iconData', JSON.stringify(iconData));
            }
        });
    }

    function saveIconData(icon) {
        // First update localStorage
        var iconData = JSON.parse(localStorage.getItem('iconData')) || {};
        iconData[icon.iconId] = icon;
        localStorage.setItem('iconData', JSON.stringify(iconData));

        // Then try to save to database
        saveIconToDatabase(icon);
    }

    function loadIconData(iconId) {
        const iconData = JSON.parse(localStorage.getItem('iconData')) || {};
        return iconData[iconId];
    }

    function openEditPopup(iconId) {
        // Load icon data from local storage
        const iconData = loadIconData(iconId);
    
        if (!iconData) {
            console.error(`Icon with ID ${iconId} not found.`);
            return;
        }
        
        const currentType = iconData.type || '';
        const currentCategory = getCategoryFromType(currentType);
        const currentPriority = getPriorityFromType(currentType);
    
        var popupContent = `
            <div>
                <label for='alert-category'>Category:</label>
                <select id='alert-category'>
                    <option value='electrical'>Electrical</option>
                    <option value='plumbing'>Plumbing</option>
                    <option value='hvac'>HVAC</option>
                    <option value='door'>Door</option>
                    <option value='stairs'>Stairs</option>
                    <option value='window'>Window</option>
                    <option value='deck'>Deck</option>
                    <option value='tree'>Tree</option>
                    <option value='other'>Other</option>
                </select>
            </div>
            <div>
                <label for='alert-priority'>Priority:</label>
                <select id='alert-priority'>
                    <option value='low'>Low</option>
                    <option value='medium'>Medium</option>
                    <option value='high'>High</option>
                </select>
            </div><br>
            <input type='file' id='icon-photo' accept='image/*'><br>
            <textarea id='icon-notes' placeholder='Enter notes'></textarea><br>
            <button class='save-button'>Save</button>
            <button class='delete-button' style='background-color: #f44336; color: white;'>Delete</button>
        `;
    
        var $popup = $("<div class='alerts'></div>");
        $popup.html(popupContent);
        $popup.dialog({
            width: 600,
            height: 'auto',
            modal: true,
            title: 'Edit Icon',
            close: function () {
                $(this).dialog('destroy').remove();
            }
        });
    
        $popup.find('#alert-category').val(currentCategory);
        $popup.find('#alert-priority').val(currentPriority);
        $popup.find('#icon-notes').val(iconData.notes);
    
        $popup.find('.save-button').on('click', function () {
            // Combine category and priority into a single type string
            const category = $popup.find('#alert-category').val();
            const priority = $popup.find('#alert-priority').val();
            const combinedType = createTypeString(category, priority);
            
            const updatedIcon = {
                ...iconData,
                type: combinedType,
                notes: $popup.find('#icon-notes').val()
            };
    
            // Update localStorage and database
            saveIconData(updatedIcon);
    
            $popup.dialog('close');
            
            // Update the icon's appearance on the page
            const $icon = $(`.box-alert[iconId="${iconId}"]`);
            if ($icon.length) {
                // Replace the icon with updated version
                $icon.remove();
                placeIcon(updatedIcon);
            }
        });
        
        // Add delete functionality
        $popup.find('.delete-button').on('click', function() {
            if (confirm('Are you sure you want to delete this icon?')) {
                // Remove from DOM
                $(`.box-alert[iconId="${iconId}"]`).remove();
                
                // Remove from localStorage
                var iconData = JSON.parse(localStorage.getItem('iconData')) || {};
                delete iconData[iconId];
                localStorage.setItem('iconData', JSON.stringify(iconData));
                
                // Try to delete from database
                $.ajax({
                    url: './php_scripts/delete_icon.php',
                    method: 'POST',
                    data: JSON.stringify({ iconId: iconId }),
                    contentType: 'application/json',
                    success: function(response) {
                        console.log('Delete response:', response);
                    },
                    error: function(xhr, status, error) {
                        console.log('Delete error (icon will still be removed from display):', error);
                    }
                });
                
                $popup.dialog('close');
            }
        });
    }    
    

    // Event listener for icon edit
    $('body').on("click", '.alerts-icon', function (e) {
        e.preventDefault();
        var iconId = $(this).closest('.box-alert').attr("iconId");
        openEditPopup(iconId);
    });

    $(".sidebar-icon").click(function() {
        const buttonId = $(this).attr("id");
        const popupId = "#" + buttonId.replace("-button", "-popup");
        
        $(".popup-menu").not(popupId).hide(); 
        $(popupId).toggle();
        
        return false; 
    });
    
    // Handle priority icon selection from popup menus
    $(".popup-icon").click(function() {
        const classes = $(this).attr("class").split(" ");
        let category = "";
        let priority = "";
        
        classes.forEach(className => {
            if (className.includes("-priority-") && className.includes("-icon")) {
                const parts = className.split("-priority-");
                priority = parts[0]; 
                category = parts[1].replace("-icon", ""); 
            }
        });
        
        if (category && priority) {
            activeButtonId = category;
            activePriorityClass = priority;
            
            $(this).closest(".popup-menu").hide();
        }
    });

    // Handle click on the main area to place icons
    $(".clickableArea").on("mousedown", function (event) {
        if (isSelectMode) {
            if (activeButtonId && activePriorityClass) {
                var bodyOffset = $("body").offset();
                var iconTop = event.pageY - bodyOffset.top - 10;
                var iconLeft = event.pageX - bodyOffset.left - 10;
                console.log("Calculated position for new icon:", iconLeft, iconTop);

                var iconId = "icon-" + Date.now() + "-" + Math.floor(Math.random() * 1000);
                
                var iconType = createTypeString(activeButtonId, activePriorityClass);
                
                var $icon = $("<div class=\"box-alert\"><div class=\"alerts-icon\"><img src=\"images/" + activePriorityClass + "-priority-" + activeButtonId + "-icon.png\"></div></div>");
                
                $icon.attr("iconId", iconId);
                $icon.css({
                    top: iconTop,
                    left: iconLeft
                });
                $(".clickableArea").append($icon);

                var icon = new Icon(iconId, iconType, null, null, iconLeft, iconTop);
                saveIconData(icon);
            } 
            // Handling for legacy buttons
            else if (activeButtonId && !activePriorityClass) {
                var bodyOffset = $("body").offset();
                var iconTop = event.pageY - bodyOffset.top - 10;
                var iconLeft = event.pageX - bodyOffset.left - 10;
                
                var iconId = "icon-" + Date.now() + "-" + Math.floor(Math.random() * 1000);
                var $icon;
                var iconType = '';

                // Determine type based on active button
                switch (activeButtonId) {
                    case "alert-severe-button":
                        $("#alert-severe-button").css("background-image", "url('images/alert-severe-button-active.png')");
                        $icon = $("<div class=\"box-alert\"><div class=\"alerts-icon\"><img src=\"images/alert-severe-icon.png\"></div></div>");
                        iconType = "other-high";
                        break;
                    case "alert-moderate-button":
                        $("#alert-moderate-button").css("background-image", "url('images/alert-moderate-button-active.png')");
                        $icon = $("<div class=\"box-alert\"><div class=\"alerts-icon\"><img src=\"images/alert-moderate-icon.png\"></div></div>");
                        iconType = "other-medium";
                        break;
                    case "alert-button":
                        $("#alert-button").css("background-image", "url('images/alert-button-active.png')");
                        $icon = $("<div class=\"box-alert\"><div class=\"alerts-icon\"><img src=\"images/alert-icon.png\"></div></div>");
                        iconType = "other-low"; 
                        break;
                }

                if ($icon) {
                    $icon.attr("iconId", iconId);
                    $icon.css({
                        top: iconTop,
                        left: iconLeft
                    });
                    $(".clickableArea").append($icon);

                    var icon = new Icon(iconId, iconType, null, null, iconLeft, iconTop);
                    saveIconData(icon);
                }
            } else {
                var $clickedIcon = $(event.target).closest('.box-alert');
                if ($clickedIcon.length) {
                    var clickedIconId = $clickedIcon.attr("iconId");
                    openEditPopup(clickedIconId);
                }
            }
        }
    });

    // Hide popup menus when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('.sidebar-icon, .popup-menu').length) {
            $(".popup-menu").hide();
        }
    });

    // Navbar and side navigation functionality
    function toggleSideNav() {
        $(".side_nav,.nav-overlay").toggleClass("active");
    }

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
        
        // Reset active values
        activeButtonId = null;
        activePriorityClass = null;
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
        activePriorityClass = null;
    });

    $("#alert-severe-button, #alert-moderate-button, #alert-button").on("click", function () {
        if (isSelectMode) {
            activeButtonId = $(this).attr("id");
            activePriorityClass = null;
        }
    });

    $("#clearButton").on("click", function () {
        let text = "Warning: All icons will be deleted\n";
        if (confirm(text)) {
            // First remove icons from the page
            $(".box, .box-alert, .box-note").remove();
            
            // Reset UI state
            deactivateIcons();
            isSelectMode = false;
            isBrushActive = false;
            $("#select-button").css("background-image", "url('images/select-button.png')");
            
            // Clear localStorage
            localStorage.removeItem('iconData');
            
            // Also attempt to delete from database if assignment ID exists
            if (assignmentID) {
                $.ajax({
                    url: './php_scripts/delete_icons.php',
                    method: 'POST',
                    data: JSON.stringify({ assignmentID: assignmentID }),
                    contentType: 'application/json',
                    success: function(response) {
                        console.log('Cleared all icons response:', response);
                    },
                    error: function(xhr, status, error) {
                        console.log('Failed to clear icons from database, but UI is cleared:', error);
                    }
                });
            }
        }
    });
});