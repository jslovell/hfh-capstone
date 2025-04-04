$(document).ready(function () {
    var activeButtonId = null;
    var selectedSeverity = null;
    var selectedType = null;

    class Icon {
        constructor(iconId, alertType, severity, photoData, notesData, x_pos, y_pos) {
            this.iconId = iconId;
            this.assignmentID = assignmentID;
            this.type = alertType;
            this.severity = severity;
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

    function getIconImageByType(type) {
        const category = getCategoryFromType(type);
        const priority = getPriorityFromType(type);
        
        let imagePath = "images/alert-icon.png";  
        
        if (category) {
            // trying to set to standardized image format - ie. low-priority-hvac-icon.png
            imagePath = `images/${priority}-priority-${category}-icon.png`;
            console.log(`Using icon image: ${imagePath} for type: ${type}`);
        }
        
        return imagePath;
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

        const { iconId, type, severity, x_pos, y_pos } = icon;

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
            left: `${x_pos}%`,
            top: `${y_pos}%`
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
        formData.append("severity", icon.severity);
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

        if (activeButtonId == "select") {
            openEditPopup(iconId);
        }
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
                    <option value='window'>Window(s)</option>
                    <option value='door'>Door(s)</option>
                    <option value='siding'>Siding</option>
                    <option value='porch'>Porch</option>
                    <option value='stairs'>Stairs</option>
                    <option value='deck'>Deck</option>
                    <option value='hvac'>Mechanical (HVAC)</option>
                    <option value='plumbing'>Plumbing</option>
                    <option value='electrical'>Electrical</option>
                    <option value='flatworks'>Flatwork</option>
                    <option value='tree'>Tree Maintenance</option>
                    <option value='roofing'>Roofing</option>
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
        $("#alert-type").val(iconInstance.type || '');

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
        if (activeButtonId == "place") {
            var iconId = "icon-" + Date.now();

            var $assessmentArea = $(".assessmentArea");
            var rect = $assessmentArea[0].getBoundingClientRect();
            var areaWidth = rect.width;
            var areaHeight = rect.height;

            var x = ((event.clientX - rect.left) / areaWidth) * 100;
            var y = ((event.clientY - rect.top) / areaHeight) * 100;

            let newIcon = new Icon(iconId, null, null, null, null, x, y);

            if (selectedType != null && selectedSeverity != null) {
                newIcon.type = `${selectedType}-${selectedSeverity}`;
                console.log("placing with type and severity:", newIcon.type);
            }

            saveIconData(newIcon);
            placeIcon(newIcon);
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

    // Updates sidebar button pictures logically based on current active button ID
    function updateSidebar() {
        if (activeButtonId == "select") {
            $("#alert-severe-button").css("background-image", "url('images/alert-severe-button.png')");
        } else if (activeButtonId == "place") {
            $("#select-button").css("background-image", "url('images/select-button.png')");
        } else if (activeButtonId == "delete") {
            $("#select-button").css("background-image", "url('images/select-button.png')");
            $("#alert-severe-button").css("background-image", "url('images/alert-severe-button.png')");
            activeButtonId = null;
        }
    }

    $("#select-button").click(function () {
        if (activeButtonId == "select") {
            $("#select-button").css("background-image", "url('images/select-button.png')");
            activeButtonId = null;
        } else {
            $("#select-button").css("background-image", "url('images/select-button-active.png')");
            activeButtonId = "select";
            updateSidebar(); // Update other buttons
        }
    });

    $("#alert-severe-button").on("click", function () {
        if (activeButtonId == "place"){
            activeButtonId == null;
            $("#alert-severe-button").css("background-image", "url('images/alert-severe-button.png')");
        } else {
            activeButtonId = "place";
            $("#alert-severe-button").css("background-image", "url('images/alert-severe-button-active.png')");
            updateSidebar();    // Update other buttons
        }
    });

    $("#clearButton").on("click", function () {
        let text = "Warning: All icons will be deleted\n";
        if (confirm(text)) {
            $(".box, .box-alert, .box-note").remove();
            deleteAllIconsFromDatabase();
            localStorage.clear();
            activeButtonId = "delete";
            updateSidebar();    // Update other buttons
        }
    });

    $(".sidebar-icon").on("click", function(e) {
        
        const popup = $(this).find('.popup-menu');
        
        if (!popup.length) return;

        $(".popup-menu.visible").not(popup).removeClass("visible");

        popup.toggleClass("visible");

        const $button = $(this);
        const buttonPosition = $button.position();
        popup.css("top", buttonPosition.top + "px");


        e.stopPropagation();
    });

    $(document).on("click", function(e) {
        if (!$(e.target).closest('.popup-menu, .sidebar-icon').length) {
            $(".popup-menu.visible").removeClass("visible");
        }
    });

    $(".popup-icon").on("click", function() {
        const classes = $(this).attr("class").split(" ");
        let priority = null;
        let category = null;
        
        classes.forEach(className => {
            if (className.includes("-priority-") && className.includes("-icon")) {
                const parts = className.split("-priority-");
                priority = parts[0]; 
                category = parts[1].replace("-icon", "");
            }
        });
        
        if (category && priority) {
            const typeString = `${category}-${priority}`;
            console.log(`Selected icon type: ${typeString}`);
            
            // Set active button ID to "place"
            activeButtonId = "place";
            selectedSeverity = priority;
            selectedType = category;
            
            // Close popup
            $(this).closest(".popup-menu").hide();
        }
    });

});
