$(document).ready(function () {
    var activeButtonId = null;
    var isBrushActive = false;
    var isSelectMode = false;

    class Icon {
        constructor(iconId, alertType, photoData, notesData, x_pos, y_pos) {
            this.id = iconId;
            this.assignmentID = assignmentID;
            this.type = alertType;
            this.photo = photoData;
            this.notes = notesData;
            this.x_pos = x_pos;
            this.y_pos = y_pos;
        }
    }

    function saveIconToDatabase(icon) {
        if (icon.x_pos === undefined || icon.y_pos === undefined) {
            console.error(`Position data is missing for icon ID: ${icon.id}`);
        }
        var iconData = {
            iconId: icon.id,
            assignmentID: icon.assignmentID,
            type: icon.type,
            photo: icon.photo,
            notes: icon.notes,
            x_pos: icon.x_pos,
            y_pos: icon.y_pos
        };
        console.log("Saving to database:", iconData);

        $.ajax({
            url: './php_scripts/save_icon.php',
            method: 'POST',
            data: JSON.stringify(iconData),
            contentType: 'application/json',
            success: function (response) {
                console.log('Icon saved:', response);
            },
            error: function (xhr, status, error) {
                console.log("Error status: " + status);
                console.log("Response text: " + xhr.responseText);
                console.error('Error saving icon:', error);
            }
        });
    }

    function saveIconData(icon) {
        var iconData = JSON.parse(localStorage.getItem('iconData')) || {};
        iconData[icon.id] = icon;
        localStorage.setItem('iconData', JSON.stringify(iconData));

        saveIconToDatabase(icon);
    }

    function loadIconData(iconId) {
        let iconData = JSON.parse(localStorage.getItem('iconData')) || {};
        let icon = iconData[iconId];

        if (icon) {
            console.log("Loaded Icon Data:", icon);
            if (icon.x_pos === undefined || icon.y_pos === undefined) {
                console.error(`Position data missing for icon with ID: ${iconId}`);
            }
        } else {
            console.log(`No data found for icon with ID: ${iconId}`);
        }

        return icon;
    }

    function openEditPopup(iconId) {
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
            title: 'Input Alert',
            overlay: { backgroundColor: "#000", opacity: 0.9 },
            close: function () {
                $(this).dialog('destroy').remove();
            }
        });

        var icon = loadIconData(iconId);
        if (icon) {
            $popup.find('#alert-type').val(icon.type);
            $popup.find('#icon-notes').val(icon.notes);
        }

        $popup.find('.save-button').on('click', function () {
            var alertType = $popup.find('#alert-type').val();
            var photoData = $popup.find('#icon-photo')[0].files[0];
            var notesData = $popup.find('#icon-notes').val();

            var iconLeft = icon ? icon.x_pos : $(`#${iconId}`).offset().left;
            var iconTop = icon ? icon.y_pos : $(`#${iconId}`).offset().top;

            var updatedIcon = new Icon(iconId, alertType, photoData ? photoData.name : "", notesData, iconLeft, iconTop);

            saveIconData(updatedIcon);
            $popup.dialog('close');
        });
    }

    // Event listener for icon edit
    $('body').on("click", '.alerts-icon', function (e) {
        e.preventDefault();
        var iconId = $(this).closest('.box-alert').attr("id");
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
                    $icon.attr("id", iconId);
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
                    var clickedIconId = $clickedIcon.attr("id");
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
