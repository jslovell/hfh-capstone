$(document).ready(function () {
    var activeButtonId = null;
    var selectedType = null;
    var selectedSeverity = null;

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

    function updateSidebarVisuals() {
        // For each sidebar button, figure out what severity and type it corresponds to.
        // Then decide if it’s “active” and swap image paths accordingly.
        $(".sidebar-icon").each(function() {
            const $button = $(this);
            
            // Grab all classes on this element
            const classes = $button.attr("class").split(/\s+/);
            let iconSeverity = null;
            let iconType = null;
    
            // Look for something like "low-priority-electrical-icon"
            classes.forEach(c => {
                const match = c.match(/^(low|medium|high)-priority-(.+)-icon$/);
                if (match) {
                    iconSeverity = match[1];
                    iconType = match[2];
                }
            });
    
            // If we couldn't find severity/type from the class, skip
            if (!iconSeverity || !iconType) {
                return;
            }
    
            // Decide if this button should be "active".
            // It's "active" if we're in 'place' mode AND
            // the severity/type matches the currently selected severity/type.
            let shouldBeActive = false;
            if (activeButtonId === "place" &&
                selectedSeverity === iconSeverity &&
                selectedType === iconType) {
                shouldBeActive = true;
            }
    
            // Find the <img> within the sidebar button (or you could use the .sidebar-icon as <img> itself)
            const $img = $button.find("img");
            if ($img.length === 0) return;
    
            // Build the *base* image path: e.g. "images/low-priority-electrical-icon.png"
            // then append "-active" if it should be active.
            let basePath = `images/${iconSeverity}-priority-${iconType}-icon`;
            if (shouldBeActive) {
                basePath += "-active";
            }
            basePath += ".png";
    
            $img.attr("src", basePath);
        });
    
        // If you also want to show whether the “select” button itself is active, handle it separately:
        if (activeButtonId === "select") {
            // Example: if you have an <img id="selectButton" src="images/select.png">
            // you can swap to “select-active.png”
            $("#selectButton").attr("src", "images/select-active.png");
        } else {
            $("#selectButton").attr("src", "images/select.png");
        }
    }
    

    function getIconImagePath(icon) {
        
        let imagePath = "images/alert-icon.png";  
        
        if (icon.type != null || icon.type != "null") {
            // trying to set to standardized image format - ie. low-priority-hvac-icon.png
            imagePath = `images/${icon.severity}-priority-${icon.type}-icon.png`;
            console.log(`Using icon image: ${imagePath} for type: ${icon.type}`);
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
        const iconSrc = getIconImagePath(icon);
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
                    <option value='flatwork'>Flatwork</option>
                    <option value='tree'>Tree Maintenance</option>
                    <option value='roofing'>Roofing</option>
                    <option value='null'>Other</option>
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
            if (file) {
                iconInstance.photoData = file;
                if (!/\.(jpg|jpeg|png)$/i.test(file.name)) {
                    alert("Invalid image type.");
                    return;
                }
            }
        
            saveIconData(iconInstance);
        
            let $iconDiv = $(`[iconId="${iconInstance.iconId}"]`);
            $iconDiv.find("img").attr("src", getIconImagePath(iconInstance));
        
            $("#edit-popup").dialog('close');
        });        

        $("#delete-button").on("click", function () {
            deleteIconFromDatabase(iconId);
            $("#edit-popup").dialog('close');
        });
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

    $(document).on("click", function(e) {
        if (!$(e.target).closest('.popout-menu, .sidebar-icon').length) {
            $(".popout-menu").removeClass("visible");
        }
    });
    
    function handlePopoutIconClick(e) {
        e.stopPropagation();
    
        const classes = $(this).attr("class").split(/\s+/);
        let sv = null, tp = null;
        classes.forEach(c => {
            const m = c.match(/^(low|medium|high)-priority-(.+)-icon$/);
            if (m) {
                sv = m[1];
                tp = m[2];
            }
        });
        if (!sv || !tp) return;
    
        selectedSeverity = sv;
        selectedType = tp;
        activeButtonId = "place";
    
        $(this).closest(".popup-menu").removeClass("visible");
    }
    
    function handleClearButtonClick(e) {
        e.stopPropagation();
        const $btn = $(this);
        const currentBg = $btn.css("background-image");

        // close popups if one is currently active
        $(".popup-menu").removeClass("visible")
        // update visuals if another tool is currently active
        $(".sidebar-icon").each(function() {
            const bg = $(this).css("background-image")
            if (bg && bg.includes("-active.png")) {
                $(this).css("background-image", bg.replace("-active.png", ".png"))
            }
        })
    
        if (currentBg.includes("-active.png")) {
            $btn.css("background-image", currentBg.replace("-active.png", ".png"));
            return;
        }
    
        $btn.css("background-image", currentBg.replace(".png", "-active.png"));
    
        // Delay so icon has chance to update before popup appears
        setTimeout(() => {
            const confirmDelete = confirm("Delete all icons on this page?");
            if (confirmDelete) {
                $(".box-alert").remove();
                localStorage.setItem("iconData", JSON.stringify([]));
                deleteAllIconsFromDatabase();
            }
            const bg2 = $btn.css("background-image");
            if (bg2.includes("-active.png")) {
                $btn.css("background-image", bg2.replace("-active.png", ".png"));
            }
        }, 150);
    }
    
    function handleSelectButtonClick(e) {
        e.stopPropagation();
        const $btn = $(this);
        const currentBg = $btn.css("background-image");

        // close popups if one is currently active
        $(".popup-menu").removeClass("visible")
        // update visuals if another tool is currently active
        $(".sidebar-icon").each(function() {
            const bg = $(this).css("background-image")
            if (bg && bg.includes("-active.png")) {
                $(this).css("background-image", bg.replace("-active.png", ".png"))
            }
        })
    
        // If it's already active, revert and clear activeButtonId
        if (currentBg.includes("-active.png")) {
            $btn.css("background-image", currentBg.replace("-active.png", ".png"));
            activeButtonId = null;
            return;
        }
    
        // Otherwise, revert all other icons to normal
        $(".sidebar-icon, #select-button, #clear-button").each(function() {
            const bg = $(this).css("background-image");
            if (bg && bg.includes("-active.png")) {
                $(this).css("background-image", bg.replace("-active.png", ".png"));
            }
        });
    
        $btn.css("background-image", currentBg.replace(".png", "-active.png"));
        activeButtonId = "select";
    }
    
    function handleSidebarIconClick(e) {
        e.stopPropagation();
        const btnId = $(this).attr("id");
    
        if (activeButtonId === "select" && btnId !== "select-button" && btnId !== "clear-button") {
            const selectBg = $("#select-button").css("background-image");
            if (selectBg && selectBg.includes("-active.png")) {
                $("#select-button").css("background-image", selectBg.replace("-active.png", ".png"));
            }
            activeButtonId = null;
        }
    
        const currentBg = $(this).css("background-image");

        if (currentBg && currentBg.includes("-active.png")) {
            $(this).css("background-image", currentBg.replace("-active.png", ".png"));
            $(this).children(".popup-menu").removeClass("visible");
        } else {
            $(".popup-menu").removeClass("visible");
            $(".sidebar-icon").each(function() {
                const bg = $(this).css("background-image");
                if (bg && bg.includes("-active.png")) {
                    $(this).css("background-image", bg.replace("-active.png", ".png"));
                }
            });
            $(this).css("background-image", currentBg.replace(".png", "-active.png"));
            $(this).children(".popup-menu").addClass("visible");
        }
    }
    

    function handleAssessmentAreaMousedown(e) {
        if (activeButtonId !== "place" || !selectedSeverity || !selectedType) return;
    
        const $area = $(".assessmentArea");
        const rect = $area[0].getBoundingClientRect();
        const xPercent = ((e.clientX - rect.left) / rect.width) * 100;
        const yPercent = ((e.clientY - rect.top) / rect.height) * 100;
    
        const iconId = "icon-" + Date.now();
        const newIcon = new Icon(
            iconId,
            selectedType,
            selectedSeverity,
            null,
            null,
            xPercent,
            yPercent
        );
    
        saveIconData(newIcon);
        placeIcon(newIcon);
    
        activeButtonId = null;
        selectedType = null;
        selectedSeverity = null;
    
        $(".sidebar-icon").each(function() {
            const bg = $(this).css("background-image");
            if (bg && bg.includes("-active.png")) {
                $(this).css("background-image", bg.replace("-active.png", ".png"));
            }
        });
    }
    
    // bind everything
    $(document).on("mousedown", ".assessmentArea", handleAssessmentAreaMousedown);
    $(document).on("click", "#clear-button", handleClearButtonClick);
    $(document).on("click", "#select-button", handleSelectButtonClick);
    $(document).on("click", ".sidebar-icon", handleSidebarIconClick);
    $(document).on("click", ".popup-icon", handlePopoutIconClick);        

});
