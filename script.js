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
        $iconDiv.attr("iconId", iconId); // Use "iconId" as the attribute key
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
    
        // Attach data to the icon
        $iconDiv.data("iconData", { iconId, type, picture, notes, x_pos, y_pos });
    
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
        var iconData = JSON.parse(localStorage.getItem('iconData')) || {};
        iconData[icon.iconId] = icon;
        localStorage.setItem('iconData', JSON.stringify(iconData));

        saveIconToDatabase(icon);
    }

    function loadIconData(iconId) {
        const iconData = JSON.parse(localStorage.getItem('iconData')) || [];
        return iconData.find(icon => icon.iconId === iconId);
    }

    function openEditPopup(iconId) {
        // Load icon data from local storage
        const iconData = loadIconData(iconId);
    
        if (!iconData) {
            console.error(`Icon with ID ${iconId} not found.`);
            return;
        }
    
        var popupContent = `
            <select id='alert-type'>
                <option value='1'>Electrical</option>
                <option value='2'>Windows</option>
                <option value='3'>Water Damage</option>
                <option value='4'>Asbestos</option>
                <option value='5'>Cracks</option>
                <option value='other'>Other</option>
            </select><br>
            <input type='file' id='icon-photo' accept='image/*'><br>
            <textarea id='icon-notes' placeholder='Enter notes'></textarea><br>
            <button class='save-button'>Save</button>
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
    
        // Pre-fill data
        $popup.find('#alert-type').val(iconData.type);
        $popup.find('#icon-notes').val(iconData.notes);
    
        $popup.find('.save-button').on('click', function () {
            const updatedIcon = {
                ...iconData,
                type: $popup.find('#alert-type').val(),
                notes: $popup.find('#icon-notes').val()
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
                console.log("Calculated position for new icon:", iconLeft, iconTop);

                var iconId = "icon-" + Date.now();
                var $icon;

                switch (activeButtonId) {
                    case "alert-severe-button":
                        $("#alert-severe-button").css("background-image", "url('images/alert-severe-button-active.png')");
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

                    var icon = new Icon(iconId, null, null, null, iconLeft, iconTop);
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
            localStorage.clear();
        }
    });
});
