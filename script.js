$(document).ready(function() {
    var activeButtonId = null;
    var isBrushActive = false;
    var isSelectMode = false;
    var iconCounter = localStorage.getItem('iconCounter') ? parseInt(localStorage.getItem('iconCounter')) : 0;
    var iconId = null;

    // AJAX function to save icon data to the server
    function saveIconDataToServer(iconData) {
        $.ajax({
            url: "save_icon_data.php", // Adjust this path to match your PHP file
            method: "POST",
            data: iconData,
            success: function(response) {
                console.log("Icon data saved:", response);
            },
            error: function(xhr, status, error) {
                console.error("Error saving icon data:", error);
            }
        });
    }

    // Alert popup
    $('body').on("click", '.alerts-icon', function(e) {
        e.preventDefault();
        var popupContent = "<select><option value='1'>Electrical</option><option value='2'>Windows</option><option value='3'>Water Damage</option><option value='4'>Asbestos</option><option value='5'>Cracks</option><option value='other'>Other</option></select><br><textarea id='alerts-textarea' placeholder='Enter description'></textarea><br><input type='file' accept='image/*'><br><button class='save-button'>Save</button>";
        var $popup = $("<div class='alerts'></div>");
        $popup.html(popupContent);
        $popup.dialog({
            width: 600,
            height: 'auto',
            modal: true,
            title: 'Input Alert',
            overlay: { backgroundColor: "#000", opacity: 0.9 }
        });

        // Save button action for Alert popup
        $popup.on('click', '.save-button', function() {
            var textareaValue = $('#alerts-textarea').val();
            var iconData = { textareaValue: textareaValue };
            saveIconData(iconId, 'alerts', iconData);
            $popup.dialog('close');
        });
    });

    // Note popup
    $('body').on("click", '.notes-icon', function(e) {
        e.preventDefault();
        var popupContent = "<textarea id='notes-textarea' placeholder='Enter description'></textarea><button class='save-button'>Save</button>";
        var $popup = $("<div class='notes'></div>");
        $popup.html(popupContent);
        $popup.dialog({
            width: 600,
            height: 'auto',
            modal: true,
            title: 'Input Notes',
            overlay: { backgroundColor: "#000", opacity: 0.9 }
        });

        // Save button action for Notes popup
        $popup.on('click', '.save-button', function() {
            var textareaValue = $('#notes-textarea').val();
            var iconData = { textareaValue: textareaValue };
            saveIconData(iconId, 'notes', iconData);
            $popup.dialog('close');
        });
    });

    // Photo popup
    $('body').on("click", '.photo-icon', function(e) {
        e.preventDefault();
        var popupContent = "<textarea id='photo-textarea' placeholder='Enter description'></textarea><br><input type='file' accept='image/*'><br><button class='save-button'>Save</button>";
        var $popup = $("<div class='photo'></div>");
        $popup.html(popupContent);
        $popup.dialog({
            width: 600,
            height: 'auto',
            modal: true,
            title: 'Input Picture',
            overlay: { backgroundColor: "#000", opacity: 0.9 }
        });

        // Save button action for Photo popup
        $popup.on('click', '.save-button', function() {
            var textareaValue = $('#photo-textarea').val();
            var iconData = { textareaValue: textareaValue };
            saveIconData(iconId, 'photo', iconData);
            $popup.dialog('close');
        });
    });

    // Additional logic for icon placement
	$(".clickableArea").on("click", function(event) {
		if (isSelectMode && activeButtonId) {
			var bodyOffset = $("body").offset();
			var iconTop = event.pageY - bodyOffset.top - 10;
			var iconLeft = event.pageX - bodyOffset.left - 10;
			var $icon;
	
			// Create and position icon based on the active button ID
			switch (activeButtonId) {
				case "alert-button":
					$icon = $("<div class=\"box-alert\"><div class=\"alerts-icon\"><img src=\"images/alert-icon.png\"></div></div>");
					break;
				case "note-button":
					$icon = $("<div class=\"box-note\"><div class=\"notes-icon\"><img src=\"images/note-icon.png\"></div></div>");
					break;
				case "photo-button":
					$icon = $("<div class=\"box\"><div class=\"photo-icon\"><img src=\"images/picture-icon.png\"></div></div>");
					break;
			}
	
			// Assign a unique ID to the icon, set position, and append to the document
			$icon.attr("id", "icon-" + iconCounter).css({ top: iconTop, left: iconLeft });
			$("body").append($icon);
	
			// Save icon to the database
			saveIconToDatabase(
				"icon-" + iconCounter,
				activeButtonId.replace("-button", ""), // Pass type
				{
					x_pos: iconLeft,
					y_pos: iconTop,
					textareaValue: "",  // Initial save without description; handled in pop-up later
					picture: null       // Picture data will be handled in the photo-icon popup
				}
			);
	
			// Increment the icon counter
			iconCounter++;
		}
	});

    // Clear all icons
    $("#clearButton").on("click", function() {
        if (confirm("Warning: All icons will be deleted")) {
            $(".box, .box-alert, .box-note").remove();
            localStorage.clear();
            iconCounter = 0;
            isSelectMode = false;
            isBrushActive = false;
            $(".icon-button").css("background-image", "url('images/default-icon.png')");
        }
    });
});
