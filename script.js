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
            this.x_pos = x_pos;
            this.y_pos = y_pos;
        }
    }

    function fetchAndPlaceIcons() {
        $.ajax({
            url: './php_scripts/load_icons.php',
            method: 'GET',
            data: { id: assignmentID },
            success: function (response) {
                if (response.success) {
                    // Save all icons to localStorage
                    const icons = response.data;
                    localStorage.setItem('iconData', JSON.stringify(icons));
    
                    // Place all icons on the page
                    icons.forEach(icon => {
                        placeIcon(icon);
                    });
                } else {
                    console.error('Error loading icons:', response.error);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error loading icons:', error, xhr.responseText);
            }
        });
    }    

    function placeIcon(icon) {
        const { iconId, type, picture, notes, x_pos, y_pos } = icon;
    
        let $iconDiv = $("<div class=\"box-alert\"><div class=\"alerts-icon\"><img></div></div>");
        $iconDiv.attr("iconId", iconId);
        $iconDiv.css({
            top: `${y_pos}px`,
            left: `${x_pos}px`,
            position: 'absolute'
        });
    
        const $img = $iconDiv.find("img");
    
        // Set the icon image based on its type
        switch (type) {
            default:
                $img.attr("src", "images/default-icon.png");
        }
    
        // Attach the Icon instance directly
        $iconDiv.data("iconInstance", icon);
    
        $(".clickableArea").append($iconDiv);
    }      

    // Check if current page is the assessment tool, if so, load icons to local database and place them on screen
    if (typeof currentPage !== 'undefined' && currentPage === 'assessment_tool') {
        fetchAndPlaceIcons();
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
            x_pos: parseInt(icon.x_pos),
            y_pos: parseInt(icon.y_pos),
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
        const icons = JSON.parse(localStorage.getItem('iconData')) || [];
        const index = icons.findIndex(item => item.iconId === icon.iconId);
        if (index > -1) {
            icons[index] = icon; // Update existing icon
        } else {
            icons.push(icon); // Add new icon
        }
        localStorage.setItem('iconData', JSON.stringify(icons));
    }
    
    function loadIconData(iconId) {
        const icons = JSON.parse(localStorage.getItem('iconData')) || [];
        return icons.find(icon => icon.iconId === iconId) || null;
    }    

    // Unused currently, but adds ability to delete individual icons if needed (Add delete button to popup window?)
    function deleteIconFromDatabase(iconId) {
        $.ajax({
            url: './php_scripts/delete_icon.php',
            method: 'POST',
            data: JSON.stringify({ iconId }),
            contentType: 'application/json',
            success: function (response) {
                console.log('Delete successful:', response);
            },
            error: function (xhr, status, error) {
                console.error('Error deleting icon:', error, xhr.responseText);
            }
        });
    }
    
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

    function openEditPopup(iconId) {
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
    
        // Proceed to populate and open the popup
        const popupContent = `
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
            <textarea id='icon-notes' placeholder='Enter notes'></textarea><br>
            <button class='save-button'>Save</button>
        `;
    
        const $popup = $("<div class='alerts'></div>");
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
    
        // Pre-fill data
        $popup.find('#alert-type').val(iconInstance.type || '');
        $popup.find('#icon-notes').val(iconInstance.notes || '');
    
        $popup.find('.save-button').on('click', function () {
            const updatedIcon = {
                ...iconInstance,
                type: $popup.find('#alert-type').val(),
                notes: $popup.find('#icon-notes').val(),
            };
    
            // Update localStorage
            const icons = JSON.parse(localStorage.getItem('iconData')) || [];
            const iconIndex = icons.findIndex(icon => icon.iconId === iconId);
            if (iconIndex > -1) {
                icons[iconIndex] = updatedIcon;
                localStorage.setItem('iconData', JSON.stringify(icons));
            }
    
            // Save to the server
            saveIconToDatabase(updatedIcon);
    
            $popup.dialog('close');
        });
    }    

    // Event listener for icon edit
    $('body').on("click", '.alerts-icon', function (e) {
        e.preventDefault();
        var iconId = $(this).closest('.box-alert').attr("iconId");
        openEditPopup(iconId);
    });

    $(".clickableArea").on("mousedown", function (event) {
        if (isSelectMode) {
            if (activeButtonId) {
                var bodyOffset = $("body").offset();
                var iconTop = event.pageY - bodyOffset.top - 10;
                var iconLeft = event.pageX - bodyOffset.left - 10;
            
                var iconId = "icon-" + Date.now();
                var $icon;
            
                switch (activeButtonId) {
                    default:
                        $icon = $("<div class=\"box-alert\"><div class=\"alerts-icon\"><img src=\"images/alert-sever-icon.png\"></div></div>");
                        break;
                }
            
                if ($icon) {
                    $icon.attr("iconId", iconId);
                    $icon.css({
                        top: iconTop,
                        left: iconLeft
                    });
                    $(".clickableArea").append($icon);
            
                    const newIcon = new Icon(iconId, null, null, null, iconLeft, iconTop);
                    saveIconData(newIcon);
                    placeIcon(newIcon);
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
