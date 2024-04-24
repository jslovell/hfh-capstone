$(document).ready(function() {
	var activeButtonId = null;
	var isBrushActive = false;
	var isSelectMode = false;
	var iconCounter = localStorage.getItem('iconCounter') ? parseInt(localStorage.getItem('iconCounter')) : 0;
	var iconId = null;

	function saveIconData(iconId, type, data) {
		var iconData = {
			id: iconId,
			type: type,
			data: data,
		};
		localStorage.setItem(iconId, JSON.stringify(iconData));
	}

	function loadIconData(iconId) {
		let iconData = JSON.parse(localStorage.getItem('iconData')) || {};
		return iconData[iconId];
	}


	$('body').on("click", '.alerts-icon', function(e) {
		e.preventDefault();
		var popupContent = "<select><option value='1'>Electrical</option><option value='2'>Windows</option><option value='3'>Water Damage</option><option value='4'>Asbestos</option><option value='5'>Cracks</option><option value='other'>Other</option></select><br><textarea id='alerts-textarea' placeholder='Enter description'></textarea><br><input type='file' accept='image/*'><br><button class='save-button'>Save</button>";
		var $popup = $("<div class='alerts'></div>");
		$popup.html(popupContent);
		$popup.dialog({
			width: 600,
			height: 'auto',
			modal:true,
			title: 'Input Alert',
			overlay: { backgroundColor: "#000", opacity: 0.9 }
		});
		$('#save-button').on('click', function() {
			var textareaValue = $('#alerts-textarea').val();
			saveIconData(iconId, 'alerts', { textareaValue: textareaValue });
		});

		var iconData = loadIconData(iconId);
		if (iconData && iconData.type === 'alerts') {
			$('#alerts-textarea').val(iconData.data.textareaValue);
		}
	});

	$('body').on("click", '.notes-icon', function(e) {
		e.preventDefault();
		var popupContent = "<textarea id='notes-textarea' placeholder='Enter description'></textarea><button class='save-button'>Save</button>";
		var $popup = $("<div class='alerts'></div>");
		$popup.html(popupContent);
		$popup.dialog({
			width: 600,
			height: 'auto',
			modal:true,
			title: 'Input Notes',
			overlay: { backgroundColor: "#000", opacity: 0.9 }
		});
		$('#save-button').on('click', function() {
			var textareaValue = $('#notes-textarea').val();
			saveIconData(iconId, 'notes', { textareaValue: textareaValue });
		});
	});

	$('body').on("click", '.photo-icon', function(e) {
		e.preventDefault();
		var popupContent = "<textarea id='photo-textarea' placeholder='Enter description'></textarea><br><input type='file' accept='image/*'><br><button class='save-button'>Save</button>";
		var $popup = $("<div class='alerts'></div>");
		$popup.html(popupContent);
		$popup.dialog({
			width: 600,
			height: 'auto',
			modal:true,
			title: 'Input Picture',
			overlay: { backgroundColor: "#000", opacity: 0.9 }
		});
		$('#save-button').on('click', function() {
			var textareaValue = $('#photo-textarea').val();
			saveIconData(iconId, 'photo', { textareaValue: textareaValue });
		});
	});

	$('body').on('click', '.save-button', function() {
		var textareaValue;
		if ($(this).closest('.alerts').length) {
			textareaValue = $('#alerts-textarea').val();
			saveIconData(iconId, 'alerts', { textareaValue: textareaValue });
		} else if ($(this).closest('.notes').length) {
			textareaValue = $('#notes-textarea').val();
			saveIconData(iconId, 'notes', { textareaValue: textareaValue });
		} else if ($(this).closest('.photo').length) {
			textareaValue = $('#photo-textarea').val();
			saveIconData(iconId, 'photo', { textareaValue: textareaValue });
		}
		$(this).closest('.alerts').dialog('close');
	});


	$(function(){

		$('.photo-icon').on("click", function(e){
			e.preventDefault();
			var popupContent = "<textarea placeholder='Enter description'></textarea><br><input type='file' accept='image/*'><br><button onclick='alert('upload complete')'>Upload</button>";
			var $popup = $("<div class='alerts'></div>");
			$popup.html(popupContent);
			$popup.dialog({
				width: 600,
				height: 'auto',
				modal:true,
				title: 'Input Picture',
				overlay: { backgroundColor: "#000", opacity: 0.9 }
			});
		});

		$('.alerts-icon').on("click", function(e){
			e.preventDefault();
			var popupContent = "<select><option value='1'>Electrical</option><option value='2'>Windows</option><option value='3'>Water Damage</option><option value='4'>Asbestos</option><option value='5'>Cracks</option><option value='other'>Other</option></select><br><textarea placeholder='Enter description'></textarea><br><input type='file' accept='image/*'><br><button>Submit</button>";
			var $popup = $("<div class='alerts'></div>");
			$popup.html(popupContent);
			$popup.dialog({
				width: 600,
				height: 'auto',
				modal:true,
				title: 'Input Alert',
				overlay: { backgroundColor: "#000", opacity: 0.9 }
			});
		});

		$('body').on("click", '.notes-icon', function(e) {
			e.preventDefault();
			var popupContent = "<textarea placeholder='Enter description'></textarea><button>Submit</button>";
			$popup.html(popupContent);
			$popup.dialog({
				width: 600,
				height: 'auto',
				modal:true,
				title: 'Input Notes',
				overlay: { backgroundColor: "#000", opacity: 0.9 }
			});
		});

	});


	function toggleSideNav() {
		$(".side_nav,.nav-overlay").toggleClass("active");
	}

	$(".nav_ico").click(function() {
		$(this).toggleClass("active");
		toggleSideNav();
		return false;
	});

	$(".nav-overlay").click(function() {
		$(".nav_ico").removeClass("active");
		toggleSideNav();
	});

	$(".side_nav a[data-target]").click(function(event) {
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
		$("#photo-button").css("background-image", "url('images/picture-button.png')");
		$("#note-button").css("background-image", "url('images/note-button.png')");
	}

	function toggleSelectMode() {
		isSelectMode = !isSelectMode;
		if (isSelectMode) {
			$("#select-button").css("background-image", "url('images/select-button-active.png')");
			toggleBrush();
		} else {
			$("#select-button").css("background-image", "url('images/select-button.png')");
			deactivateIcons()
			isBrushActive = false;
		}
	}

	$("#select-button").click(function() {
		toggleSelectMode();
	});

	$(".icon-button").on("click", function() {
		if (isSelectMode) {
			activeButtonId = $(this).attr("id");
		}
	});

	$("#alert-severe-button, #alert-moderate-button, #alert-button, #photo-button, #note-button, #removal-button").on("click", function() {
		if (isSelectMode) {
			activeButtonId = $(this).attr("id");
		}
	});

	$(".clickableArea").on("click", function(event) {


		if (isSelectMode && activeButtonId) {
			var bodyOffset = $("body").offset();
			var iconTop = event.pageY - bodyOffset.top - 10;
			var iconLeft = event.pageX - bodyOffset.left - 10;
			var $icon;
			switch(activeButtonId) {
				case "alert-severe-button":
					deactivateIcons()
					$("#alert-severe-button").css("background-image", "url('images/alert-severe-button-active.png')");
					$icon = $("<div class=\"box-alert\"><div class=\"alerts-icon\"><img src=\"images/alert-sever-icon.png\"></div></div>");
					$icon.attr("id", "icon-" + iconCounter);
					iconCounter++;
					$icon.css({
						top: iconTop,
						left: iconLeft
					});
					break;
				case "alert-moderate-button":
					deactivateIcons()
					$("#alert-moderate-button").css("background-image", "url('images/alert-moderate-button-active.png')");
					$icon = $("<div class=\"box-alert\"><div class=\"alerts-icon\"><img src=\"images/alert-moderate-icon.png\"></div></div>");
					$icon.attr("id", "icon-" + iconCounter);
					iconCounter++;
					$icon.css({
						top: iconTop,
						left: iconLeft
					});
					break;
				case "alert-button":
					deactivateIcons()
					$("#alert-button").css("background-image", "url('images/alert-button-active.png')");
					$icon = $("<div class=\"box-alert\"><div class=\"alerts-icon\"><img src=\"images/alert-icon.png\"></div></div>");
					$icon.attr("id", "icon-" + iconCounter);
					iconCounter++;
					$icon.css({
						top: iconTop,
						left: iconLeft
					});
					break;
				case "photo-button":
					deactivateIcons()
					$("#photo-button").css("background-image", "url('images/picture-button-active.png')");
					$icon = $("<div class=\"box\"><div class=\"photo-icon\"><img src=\"images/picture-icon.png\"></div><div class=\"picture\">Photo Here...<div></div>");
					$icon.attr("id", "icon-" + iconCounter);
					iconCounter++;
					$icon.css({
						top: iconTop,
						left: iconLeft
					});

					break;
				case "note-button":
					deactivateIcons()
					$("#note-button").css("background-image", "url('images/note-button-active.png')");
					$icon = $("<div class=\"box-note\"><div class=\"notes-icon\"><img src=\"images/note-icon.png\"></div><div class=\"notes\"></div></div>");
					$icon.attr("id", "icon-" + iconCounter);
					iconCounter++;
					$icon.css({
						top: iconTop,
						left: iconLeft
					});
					break;
			}

			$("body").append($icon);
			saveIconData($icon.attr("id"), 'icon', { top: iconTop, left: iconLeft });
			localStorage.setItem('iconCounter', iconCounter.toString());
		}

		iconId = $icon.attr("id");
	});


	$("#clearButton").on("click", function() {
		let text = "Warning: All icons will be deleted\n";
		if (confirm(text)) {
			$(".box, .box-alert, .box-note").remove();
			deactivateIcons();
			isSelectMode = false;
			isBrushActive = false;
			iconCounter = 0;
			$("#select-button").css("background-image", "url('images/select-button.png')");
			localStorage.clear();
		}
	});
});
