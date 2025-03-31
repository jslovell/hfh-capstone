$(document).ready(function () {
    var activeButtonId = null;
    var activeCategory = null; 
    var activePriority = null;

    class Icon {
        constructor(iconId, category, priority, type, photoData, notesData, x_pos, y_pos) {
            this.iconId = iconId;
            this.assignmentID = assignmentID;
            this.category = category;
            this.priority = priority;
            this.type = type;
            this.photo = photoData;
            this.notes = notesData;
            this.x_pos = x_pos;
            this.y_pos = y_pos;
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
                    const parsedResponse = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (parsedResponse.success && parsedResponse.data && parsedResponse.data.length > 0) {
                        console.log("Icons data received:", parsedResponse.data);
                        localStorage.setItem('iconData', JSON.stringify(parsedResponse.data));
                        parsedResponse.data.forEach(icon => {
                            placeIcon(icon);
                        });
                    } else {
                        console.warn("No icons found or error loading icons:", 
                            parsedResponse.error || "Invalid response format");
                    }
                } catch (e) {
                    console.error("Error processing response:", e);
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
    setTimeout(function () {
        console.log("Checking assignmentID before fetching icons:", assignmentID);

        if (typeof assignmentID !== "undefined" && assignmentID !== null) {
            console.log("Fetching icons for assignmentID:", assignmentID);
            fetchAndPlaceIcons();
        } else {
            console.error("assignmentID is not defined or is null!");
        }
    }, 100);

    function placeIcon(icon) {
        console.log("Placing icon:", icon);
    
        const { iconId, category, priority, x_pos, y_pos } = icon;
        
        if (!iconId) {
            console.error("Error: Attempted to place an icon without an iconId!");
            return;
        }
        
        let $iconDiv = $("<div class='box'></div>");
        $iconDiv.attr("iconId", iconId);
        
        let iconClass;
        
        if (category === "alert-severe") {
            iconClass = "alert-severe-icon";
        } else if (category && priority) {
            iconClass = `${priority}-priority-${category}-icon`;
        } else if (icon.type) {
            const iconSrc = getIconImageByType(icon.type);
            let $innerDiv = $("<div class='alerts-icon'></div>");
            let $img = $("<img>").attr("src", iconSrc);
            $innerDiv.append($img);
            $iconDiv.append($innerDiv);
            $iconDiv.data("iconInstance", icon);
            
            var $assessmentArea = $(".assessmentArea");
            $assessmentArea.append($iconDiv);
            
            $iconDiv.css({
                left: `${x_pos}%`,
                top: `${y_pos}%`
            });
            
            console.log(`Legacy icon ${iconId} placed at (${x_pos}%, ${y_pos}%)`);
            return;
        } else {
            console.error("Error: Icon has no valid category/priority or type.");
            return;
        }
        
        let $innerDiv = $("<div></div>").addClass(iconClass);
        $iconDiv.append($innerDiv);
        
        $iconDiv.data("iconInstance", icon);
        
        var $assessmentArea = $(".assessmentArea");
        if ($assessmentArea.length === 0) {
            console.error("Error: .assessmentArea not found!");
            return;
        }
        
        $assessmentArea.append($iconDiv);
        
        $iconDiv.css({
            left: `${x_pos}%`,
            top: `${y_pos}%`
        });
        
        console.log(`Icon ${iconId} (${priority} ${category}) placed at (${x_pos}%, ${y_pos}%)`);
    }

    function saveIconToDatabase(icon) {
        if (!icon || !icon.iconId) {
            console.error('Error: Missing icon ID when saving:', icon);
            return;
        }
    
        const formData = new FormData();
        formData.append("iconId", icon.iconId);
        formData.append("assignmentID", assignmentID);
        formData.append("category", icon.category || "");
        formData.append("priority", icon.priority || "");
        formData.append("type", icon.type || "");
        formData.append("notes", icon.notes || "");
        formData.append("x_pos", icon.x_pos);
        formData.append("y_pos", icon.y_pos);
    
        if (icon.photoData) {
            formData.append("photo", icon.photoData);
        }
    
        console.log("Sending icon data to save:", formData);
    
        $.ajax({
            url: './php_scripts/save_icon.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                try {
                    let r = typeof response === 'string' ? JSON.parse(response) : response;
                    if (r.success && r.fileName) {
                        icon.picture = r.fileName;
                        let icons = JSON.parse(localStorage.getItem('iconData')) || [];
                        let i = icons.findIndex(o => o.iconId === icon.iconId);
                        if (i > -1) icons[i].picture = r.fileName;
                        localStorage.setItem('iconData', JSON.stringify(icons));
                    }
                } catch (e) {
                    console.error("Error processing save response:", e);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error saving icon:", error);
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
        console.log("LocalStorage updated:", icons);

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
                    let parsedResponse = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (parsedResponse.success) {
                        console.log(`Successfully deleted icon ${iconId} from the database.`);

                        let icons = JSON.parse(localStorage.getItem('iconData')) || [];
                        icons = icons.filter(icon => icon.iconId !== iconId);
                        localStorage.setItem('iconData', JSON.stringify(icons));

                        $(`[iconId="${iconId}"]`).remove();
                        console.log(`Icon ${iconId} removed from screen.`);
                    } else {
                        console.error(`Error deleting icon ${iconId}:`, parsedResponse.error);
                    }
                } catch (e) {
                    console.error("Error parsing JSON response from delete:", e);
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
                let parsedResponse = typeof response === 'string' ? JSON.parse(response) : response;
                
                if (parsedResponse.success) {
                    console.log('All icons deleted successfully:', parsedResponse.message);
                } else {
                    console.error('Error deleting all icons:', parsedResponse.error);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX error deleting all icons:', error, xhr.responseText);
            }
        });
    }

    $(document).on("click", ".box", function (e) {
        if (activeButtonId === "select") {
            var iconId = $(this).attr("iconId");
            
            if (!iconId) {
                console.error("Error: Clicked an icon but it has no valid iconId.");
                return;
            }
        
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

    // Fixed mousedown event handler for assessment area
    $(document).on("mousedown", ".assessmentArea", function (event) {
        if (activeButtonId === "place" && activeCategory && activePriority) {
            console.log(`Creating new ${activePriority} priority ${activeCategory} icon`);
            
            var iconId = "icon-" + Date.now();
            var $assessmentArea = $(".assessmentArea");
            var rect = $assessmentArea[0].getBoundingClientRect();
            var areaWidth = rect.width;
            var areaHeight = rect.height;
            
            var x = ((event.clientX - rect.left) / areaWidth) * 100;
            var y = ((event.clientY - rect.top) / areaHeight) * 100;
            
            // Create new icon with correct parameters
            const newIcon = {
                iconId: iconId,
                assignmentID: assignmentID,
                category: activeCategory,
                priority: activePriority,
                type: null,
                photo: null,
                notes: "",
                x_pos: x,
                y_pos: y
            };
            
            saveIconData(newIcon);
            placeIcon(newIcon);
            console.log(`Placed ${activePriority} priority ${activeCategory} icon at (${x}%, ${y}%)`);
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

    function updateSidebar() {
        if (activeButtonId === "select") {
            $("#alert-severe-button").css("background-image", "url('images/alert-severe-button.png')");
        } else if (activeButtonId === "place") {
            $("#select-button").css("background-image", "url('images/select-button.png')");
        } else if (activeButtonId === "delete") {
            $("#select-button").css("background-image", "url('images/select-button.png')");
            $("#alert-severe-button").css("background-image", "url('images/alert-severe-button.png')");
            activeButtonId = null;
        }
    }

    $("#select-button").click(function () {
        if (activeButtonId === "select") {
            $("#select-button").css("background-image", "url('images/select-button.png')");
            activeButtonId = null;
        } else {
            $("#select-button").css("background-image", "url('images/select-button-active.png')");
            activeButtonId = "select";
            updateSidebar(); // Update other buttons
        }
    });

    $("#alert-severe-button").on("click", function () {
        if (activeButtonId === "place") {
            activeButtonId = null;  // Fixed syntax error (was using == instead of =)
            $("#alert-severe-button").css("background-image", "url('images/alert-severe-button.png')");
        } else {
            activeButtonId = "place";
            activeCategory = "alert-severe";
            activePriority = "high";
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

    $(".popup-icon").on("click", function() {
        const popupId = $(this).closest(".popup-menu").attr("id");
        activeCategory = popupId.replace("-popup", "");
        
        if ($(this).hasClass("low-priority-" + activeCategory + "-icon")) {
            activePriority = "low";
        } else if ($(this).hasClass("medium-priority-" + activeCategory + "-icon")) {
            activePriority = "medium";
        } else if ($(this).hasClass("high-priority-" + activeCategory + "-icon")) {
            activePriority = "high";
        }
        
        activeButtonId = "place";
        
        $(".sidebar-icon").removeClass("active");
        $("#" + activeCategory + "-button").addClass("active");
        
        $(".popup-menu").removeClass("visible");
        
        console.log(`Ready to place ${activePriority} priority ${activeCategory} icon`);
    });
});