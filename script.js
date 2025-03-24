$(document).ready(function () {
    var activeButtonId = null;
    var isBrushActive = false;
    var isSelectMode = false;
    var selectedIconType = null;

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

    function getIconImageByType(type) {
        switch (type) {
            case '1':   // Windows
                return 'images/alert-icon.png';
            case '2':   // Doors
                return 'images/alert-icon.png';
            case '3':   // Siding
                return 'images/alert-icon.png';
            case '4':   // Porch
                return 'images/alert-icon.png';
            case '5':   // Stairs
                return 'images/alert-moderate-icon.png';
            case '6':   // Deck
                return 'images/alert-moderate-icon.png';
            case '7':   // Mechanical
                return 'images/alert-moderate-icon.png';
            case '8':   // Plumbing
                return 'images/alert-moderate-icon.png';
            case '9':   // Electrical
                return 'images/alert-moderate-icon.png';
            case '10':  // Flatwork
                return 'images/alert-sever-icon.png';
            case '11':  // Tree Maintenance
                return 'images/alert-sever-icon.png';
            case '12':  // Roofing
                return 'images/alert-sever-icon.png';
            case 'other':
                return 'images/alert-sever-icon.png';
            default:
                // If no recognized type, use a default alert icon
                return 'images/alert-icon.png';
        }
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
        }, 10);
    });

    function placeIcon(icon) {
        console.log("Placing icon:", icon);

        const { iconId, type, x_pos, y_pos } = icon;

        if (!iconId) {
            console.error("Error: Attempted to place an icon without an iconId!");
            return;
        }

        let $iconDiv = $("<div class='box-alert'><div class='alerts-icon'><img></div></div>");
        $iconDiv.attr("iconId", iconId); // Ensure iconId is set

        const $img = $iconDiv.find("img");
        const iconSrc = getIconImageByType(type);
        $img.attr("src", iconSrc);
        $iconDiv.data("iconInstance", icon);

        var $assessmentArea = $(".assessmentArea");
        if ($assessmentArea.length === 0) {
            console.error("Error: .assessmentArea not found!");
            return;
        }

        console.log("Appending icon:", iconId, "to", $assessmentArea);
        $assessmentArea.append($iconDiv);

        $iconDiv.css({
            position: "absolute",
            left: x_pos + "%",
            top: y_pos + "%"
        });

        console.log(`Icon ${iconId} placed at (${x_pos}%, ${y_pos}%)`);
    }

    function saveIconToDatabase(icon) {
        if (!icon || !icon.iconId) {
            console.error('Error: Missing icon ID when saving:', icon);
            return;
        }

        const formData = new FormData();
        formData.append("iconId", icon.iconId);
        formData.append("assignmentID", assignmentID);
        formData.append("type", icon.type);
        formData.append("notes", icon.notes || "");
        formData.append("x_pos", icon.x_pos);
        formData.append("y_pos", icon.y_pos);

        if (icon.photoData) {
            formData.append("photo", icon.photoData);
        }

        console.log("Sending icon data to save:", Object.fromEntries(formData.entries()));

        $.ajax({
            url: './php_scripts/save_icon.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                try {
                    let r = JSON.parse(response);
                    if (r.success && r.fileName) {
                        icon.picture = r.fileName;
                        let icons = JSON.parse(localStorage.getItem('iconData')) || [];
                        let i = icons.findIndex(o => o.iconId === icon.iconId);
                        if (i > -1) icons[i].picture = r.fileName;
                        localStorage.setItem('iconData', JSON.stringify(icons));
                    }
                } catch (e) {}
            }
        });
    }

    function saveIconData(icon) {
        if (!icon || !icon.iconId) {
            console.error("Error: Attempted to save an icon without an iconId!", icon);
            return;
        }

        const icons = JSON.parse(localStorage.getItem('iconData')) || [];
        const i = icons.findIndex(o => o.iconId === icon.iconId);

        if (i > -1) {
            icons[i] = { ...icons[i], ...icon };
        } else {
            icons.push(icon);
        }
        localStorage.setItem('iconData', JSON.stringify(icons));
        console.log("LocalStorage updated:",icons);

        saveIconToDatabase(icon);
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
            data: JSON.stringify({ iconId: iconId }),
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

        openEditPopup(iconId);
    });

    function openEditPopup(iconId) {
        let iconInstance = loadIconData(iconId);
        if (!iconInstance) return;

        const oldFileName = (
            iconInstance.picture &&
            iconInstance.picture !== "null"
        ) ? iconInstance.picture : "";

        const imagePath = oldFileName
            ? `uploads/photos/${oldFileName}?t=${Date.now()}`
            : "";

        let popupContent = `
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
                ${iconInstance.picture ? `<img id="icon-preview" src="${imagePath}" style="max-width:90%;margin:20px auto;display:block;">` : ''}
                <input type='file' id='icon-photo' accept='image/*' style="display:block;margin:10px auto;">
                <label for="icon-notes">Notes:</label>
                <textarea id="icon-notes" style="display:block;margin:10px auto;">${iconInstance.notes || ''}</textarea>
                <button id="save-button" style="margin-right:10px;">Save</button>
                <button id="delete-button">Delete</button>
            </div>
        `;
        $("body").append(popupContent);
        $("#alert-type").val(baseType);
        $("#priority-level").val(priority);

        $("#edit-popup").dialog({
            width: 500,
            height: "auto",
            modal: true,
            title: "Edit Icon",
            draggable: true,
            resizable: false,
            close: function() {
                $(this).dialog("destroy").remove();
            }
        });

        $("#icon-photo").on("change", function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    if (!$("#icon-preview").length) {
                        $("#icon-photo").before(`<img id="icon-preview" src="${e.target.result}" style="max-width:90%;margin:20px auto;display:block;">`);
                    } else {
                        $("#icon-preview").attr('src', e.target.result).show();
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        $("#save-button").on("click", function () {
            iconInstance.type = $("#alert-type").val();
            iconInstance.notes = $("#icon-notes").val();

            const file = $("#icon-photo")[0].files[0];
            let isValidFile = true;

            if (file) {
                iconInstance.photoData = file;
                // Check for correct file types
                let extension = iconInstance.photoData.name.substring(iconInstance.photoData.name.length - 4);
                if (extension != ".jpg" && extension != "jpeg" && extension != ".png") {
                    console.error("Error: Invalid image type: " + extension);
                    alert("Error: Invalid image type: " + extension);
                    isValidFile = false;
                } else {
                    isValidFile = true;
                }
            } else {
                iconInstance.picture = oldFileName;
                isValidFile = true;
            }

            if (isValidFile) {
                saveIconData(iconInstance);

                let $iconDiv = $(`[iconId="${iconInstance.iconId}"]`);
                $iconDiv.find("img").attr("src", getIconImageByType(iconInstance.type));
                $("#edit-popup").dialog('close');
                setTimeout(function() {
                    fetchAndPlaceIcons();
                  }, 300);
            }
        });

        $("#delete-button").on("click", function () {
            deleteIconFromDatabase(iconId);
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

            var x = ((event.clientX - rect.left) / areaWidth) * 100;
            var y = ((event.clientY - rect.top) / areaHeight) * 100;

            const newIcon = new Icon(iconId, null, null, null, x, y);

            saveIconData(newIcon);
            placeIcon(newIcon);
        }
    });

    // Navbar and side navigation functionality
    function toggleSideNav() {
        $(".side_nav,.nav-overlay").toggleClass("active");
    }

    $(window).on("resize", function () {

        const $assessmentArea = $(".assessmentArea");
        const $assessmentImg = $("#assessment-img");

        const newWidth = $assessmentImg.width();
        const newHeight = $assessmentImg.height();

        const icons = JSON.parse(localStorage.getItem('iconData')) || [];
        $assessmentArea.empty();
        $assessmentArea.append($assessmentImg);

        icons.forEach(icon => {
            const adjustedX = (icon.x_pos / 100) * newWidth;
            const adjustedY = (icon.y_pos / 100) * newHeight;

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
