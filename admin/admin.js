$(document).ready(loadAll);

function loadAll() {
    loading(true, "#nav-accordion", "#nav-load");
	loading(true, "#section-accordion", "#section-load");
    
	//load sections
    setTimeout(function() {$.getJSON("service.php?req=1", null, loadSections).fail(catchAjaxError);}, 250);
}

function loadSections(responseJSON, status, xhr) {
	if (isResponseSuccess(responseJSON)) {
		var sectionJSON = responseJSON[1];
		
		var parent = $('#section-accordion');
		parent.empty();
        sections = [];
        
		var addButton = $("<button/>").attr({
			class: "btn btn-primary btn-add"
		});
		addButton.append("Add Section");
		addButton.bind('click', {}, addSectionClickHandler);
		parent.append(addButton);
		
		for (var i = 0; i < sectionJSON.length; i++) {
			var section = sectionJSON[i];
			var cutDownSection = {id:section.id, title:section.title};
			sections.push(cutDownSection);
			
			var handle = $("<span />").attr({
				class: "orderable-handle glyphicon glyphicon-resize-vertical"
			});
			
			//create header items
			var newAnchor = $("<a data-toggle='collapse' data-parent='#section-accordion' aria-expanded='false' aria-controls='collapse-" + section.id + "' \>").attr({
				class: "collapsed",
				role: "button",
				href: "#section-collapse-" + section.id
			});
			newAnchor.append(section.title);
			
			var newHeader = $('<h4 />').attr({
				class: "panel-title"
			});
			newHeader.append(handle).append(newAnchor);
			
			var newHeaderDiv = $('<div />').attr({
				class: "panel-heading",
				role: "tab",
				id: "section-heading-" + section.id
			});
			newHeaderDiv.append(newHeader);
			newHeaderDiv.bind('click', { elementId: "#section-collapse-" + section.id, parentId: "#section-accordion" }, toggleElementCollapse); 
			
			//create body items
			var newBodyDiv = $('<div />').attr({
				class: "panel-body"
			});
			
			addSectionTitleAndTypeForm(newBodyDiv, section, true);
			
			var newButtonInputDiv = $('<div />').attr({
				id: "section-button-text-div-" + section.id,
				class: "input-group"
			});
			
			var newButtonInputSpan = $('<span />').attr({
				class: "input-group-addon"
			});
			newButtonInputSpan.append("Button Text");
			
			newButtonTextInput = $('<input aria-describedby="basic-addon1">').attr({
				id: "section-button-text-" + section.id,
				type: "text",
				class: "form-control",
				value: section.buttonText
			});
			newButtonInputDiv.append(newButtonInputSpan).append(newButtonTextInput);
			
			if (section.type != "JOIN" && section.type != "MODS") {
				newButtonInputDiv.css("display", "none");
			}
			
			newBodyDiv.append(newButtonInputDiv);
			
			var textEditor  = $("<div />").attr({
				id: "section-editor-" + section.id,
				class: "hidden"
			});
			
			textEditor.append(section.text);
			newBodyDiv.append(textEditor);
			
			var mediaDiv  = $("<div />").attr({
				id: "section-media-" + section.id
			});
			
			if (section.type != "MEDIA") {
				mediaDiv.css("display", "none");
			}
			
			var ytListHeader = $('<h4 />').append("Youtube Accounts");
			
			var ytList  = $("<ul aria-multiselectable='true' />").attr({
				id: "section-youtubes-" + section.id,
				class: "sub-list panel-group",
				role: "tablist"
			});
			
			var ytButton = $("<button/>").attr({
				class: "btn btn-primary btn-add"
			});
			ytButton.append("Add Youtube Channel");
			ytButton.bind('click', { sectionId: section.id }, addYoutubeClickHandler);
			ytList.append(ytButton);
			
			for (var j = 0; j < section.youtubes.length; j++) {
				var youtube = section.youtubes[j];
				buildYoutubeListItem(section.youtubes[j], ytList, section.id);
			}
			
			Sortable.create(ytList[0], {
				handle: '.yt-orderable-handle',
				animation: 150,
				store: {
					get: function (sortable) {
						return [];
					},
			
					set: function (sortable) {
						var order = {order: sortable.toArray()};
						$.post("service.php?save=4", order, null, "json").fail(catchAjaxError);
					}
				}
			});
			
			var imageListHeader = $('<h4 />').append("Screenshots");
			
			var imageList  = $("<ul aria-multiselectable='true' />").attr({
				id: "section-images-" + section.id,
				class: "panel-group",
				role: "tablist"
			});
			
			var imageButton = $("<button/>").attr({
				class: "btn btn-primary btn-add"
			});
			imageButton.append("Add Screenshot");
			imageButton.bind('click', { sectionId: section.id }, addImageClickHandler);
			imageList.append(imageButton);
			
			for (var j = 0; j < section.images.length; j++) {
				buildImageListItem(section.images[j], imageList, section.id);
			}
			
			Sortable.create(imageList[0], {
				handle: '.img-orderable-handle',
				animation: 150,
				store: {
					get: function (sortable) {
						return [];
					},
			
					set: function (sortable) {
						var order = {order: sortable.toArray()};
						$.post("service.php?save=5", order, null, "json").fail(catchAjaxError);
					}
				}
			});
			
			mediaDiv.append(ytListHeader).append(ytList).append(imageListHeader).append(imageList);
			newBodyDiv.append(mediaDiv);
			
			var saveButton = $("<button />").attr({
				id: "section-save-" + section.id,
				class: "btn btn-success btn-left"
			});
			saveButton.bind('click', { sectionId: section.id }, saveSection);
			
			var saveSpan = $("<span />").attr({
				class: "glyphicon glyphicon-ok"
			});
			
			saveButton.append(saveSpan).append(" Save");
			newBodyDiv.append(saveButton);
			
			var deleteButton = $("<button />").attr({
				id: "section-delete-" + section.id,
				class: "btn btn-danger btn-right"
			});
			deleteButton.bind('click', { sectionId: section.id }, deleteSection);
			
			var deleteSpan = $("<span />").attr({
				class: "glyphicon glyphicon-remove"
			});
			
			deleteButton.append(deleteSpan).append(" Delete");
			newBodyDiv.append(deleteButton);
			
			var collapseDiv = $("<div aria-labelledby='section-heading-" + section.id + "' />").attr({
				id: "section-collapse-" + section.id,
				class: "panel-collapse collapse",
				role: "tabpanel"
			});
			collapseDiv.append(newBodyDiv);
		
			var newListItem = $("<li data-id='" + section.id + "' data-name='" + section.title + "'></li>").attr({
				id: "section-li-" + section.id,
				class: "panel panel-default"
			});
			newListItem.append(newHeaderDiv).append(collapseDiv);
			
			parent.append(newListItem);
			
			if (section.type === "TEXT" || section.type == "INTRO" || section.type == "JOIN" || section.type == "MODS") {
				textEditor.summernote({
					height: 300,
					minHeight: 50,
					maxHeight: null
				});
			}
		}
        Sortable.create(parent[0], {
            handle: '.orderable-handle',
            animation: 150,
            store: {
                get: function (sortable) {
                    return [];
                },
        
                set: function (sortable) {
                    var order = {order: sortable.toArray()};
                    $.post("service.php?save=6", order, null, "json").fail(catchAjaxError);
                }
            }
        });
        setTimeout(function() {$.getJSON("service.php?req=0", null, loadNavItems);}, 250);
        loading(false, "#section-accordion", "#section-load");
	}
	else {
        displayAlert(true, responseJSON[1]);
	}
}

function loadNavItems(responseJSON, status, xhr) {
	if (isResponseSuccess(responseJSON)) {
		var navItems = responseJSON[1];
		var navList = $("#nav-accordion");
		navList.empty();
		
		var addButton = $("<button/>").attr({
			class: "btn btn-primary btn-add"
		});
		addButton.append("Add Navigation Item");
		addButton.bind('click', { }, addNavItemClickHandler);
		navList.append(addButton);
		
		for (var i = 0; i < navItems.length; i++) {
			buildNavListItem(navItems[i], navList);
		}
		
		Sortable.create(navList[0], {
			handle: '.orderable-handle',
			animation: 150,
			store: {
				get: function (sortable) {
					return [];
				},
				set: function (sortable) {
					var order = {order: sortable.toArray()};
					$.post("service.php?save=1&subnav=0", order, null, "json").fail(catchAjaxError);
				}
			}
		});
	}
    else {
        displayAlert(true, responseJSON[1]);
    }
	loading(false, "#nav-accordion", "#nav-load");
}

function loading(value, accordion, loadSection) {
	if (value) {
		$(accordion).fadeOut("fast", function() {
			$(loadSection).fadeIn("fast");
		});
	}
	else {
		$(loadSection).fadeOut("fast", function() {
			$(accordion).fadeIn("fast");
		});
	}
}

function toggleElementCollapse(event) {
	var data = event.data;
	$(data.elementId).collapse( {parent: data.parentId, toggle: false} );
	$(data.elementId).collapse('toggle');
}

function changeNavType(event) {
	var data = event.data;
	var newValue = $("#" + data.subNavPrefix + "nav-item-type-" + data.navItemId).val();
	
	if (newValue == "SECTION") {
		$("#"+ data.subNavPrefix + "nav-item-link-div-" + data.navItemId + ", #" + data.subNavPrefix + "nav-item-sub-nav-div-" + data.navItemId).fadeOut("fast").promise().done(function() {
			$("#" + data.subNavPrefix + "nav-item-section-div-" + data.navItemId).fadeIn("fast");
		});
	}
	else if (newValue == "LINK") {
		$("#"+ data.subNavPrefix + "nav-item-section-div-" + data.navItemId + ", #" + data.subNavPrefix + "nav-item-sub-nav-div-" + data.navItemId).fadeOut("fast").promise().done(function() {
			$("#" + data.subNavPrefix + "nav-item-link-div-" + data.navItemId).fadeIn("fast");
		});
	}
	else if (newValue == "DROPDOWN") {
		$("#"+ data.subNavPrefix + "nav-item-link-div-" + data.navItemId + ", #" + data.subNavPrefix + "nav-item-section-div-" + data.navItemId).fadeOut("fast").promise().done(function() {
			$("#" + data.subNavPrefix + "nav-item-sub-nav-div-" + data.navItemId).fadeIn("fast");
		});
	}
}

function changeSectionType(event) {
	var data = event.data;
	var newValue = $("#section-type-" + data.sectionId).val();
	
	if (newValue == "TEXT" || newValue == "JOIN" || newValue == "MODS" || newValue == "INTRO") {
		$("#section-media-" + data.sectionId).fadeOut("fast", function() {
			if (newValue == "JOIN" || newValue == "MODS") {
				$("#section-button-text-div-" + data.sectionId).fadeIn("fast", function() {
					$("#section-editor-" + data.sectionId).summernote({
						height: 300,
						minHeight: 50,
						maxHeight: null
					});
				});
			}
			else {
				$("#section-button-text-div-" + data.sectionId).fadeOut("fast", function() {
					$("#section-editor-" + data.sectionId).summernote({
						height: 300,
						minHeight: 50,
						maxHeight: null
					});
				});
			}
			
		});
	}
	else if (newValue == "MEDIA") {
		$("#section-editor-" + data.sectionId).summernote('destroy');
		$("#section-button-text-div-" + data.sectionId).fadeOut("fast", function() {
			$("#section-media-" + data.sectionId).fadeIn("fast");
		});
	}
}

function saveNavItem(event) {
	var data = event.data;
	var subNavPrefix = data.subNav ? 'sub-' : ''
	var navItemId = data.navItemId;
	
	var navItemTitle = $("#" + subNavPrefix + "nav-item-title-" + navItemId).val();
	$("#" + subNavPrefix + "nav-item-header-" + navItemId).empty().append(navItemTitle);
	
	var navItemType = $("#" + subNavPrefix + "nav-item-type-" + data.navItemId).val();
	var navItemContent = getNavContent(navItemId, navItemType, subNavPrefix);
	var navItem = {
		id: navItemId,
		title: navItemTitle,
		type: navItemType,
		content: navItemContent
	};
				   
	$.post("service.php?save=0&subnav=" + Number(data.subNav), navItem, navItemSaved, "json").fail(catchAjaxError);
}

function saveSection(event) {
	var data = event.data;
	var type = $("#section-type-" + data.sectionId).val();
	var section = { id: data.sectionId, type: type, title: $("#section-title-" + data.sectionId).val() };
	
	if (type == "TEXT" || type == "INTRO" || type == "JOIN" || type == "MODS") {
		var textInput = $("#section-editor-" + data.sectionId);
		section.text = textInput.summernote('code');
		
		if (type == "JOIN" || type == "MODS") {
			section.buttonText = $("#section-button-text-" + data.sectionId).val();
		}
	}
	
	$.post("service.php?save=7", section, sectionSaved, "json").fail(catchAjaxError);
}

function navItemSaved(responseJSON, status, xhr) {
	if (isResponseSuccess(responseJSON)) {
		displayAlert(false, responseJSON[1]);
	}
    else {
		newErrors = ["Navigation item failed to save"];
        displayAlert(true, newErrors.concat(responseJSON[1]));
    }
}

function saveYoutube(event) {
	var data = event.data;
	var youtubeId = data.youtubeId;
	var title = $("#section-youtube-name-" + youtubeId);
	$("#yt-title-" + youtubeId).empty().append(title.val());

	var youtube = {
					id: youtubeId,
					channelName: title.val(),
					channelUrl: $("#section-youtube-url-" + youtubeId).val()
			    };
				   
	$.post("service.php?save=8", youtube, youtubeSaved, "json").fail(catchAjaxError);
}

function sectionSaved(responseJSON, status, xhr) {
	if (isResponseSuccess(responseJSON)) {
		displayAlert(false, responseJSON[1]);
		loadAll();
	}
    else {
		newErrors = ["Section failed to save"];
        displayAlert(true, newErrors.concat(responseJSON[1]));
    }
}

function youtubeSaved(responseJSON, status, xhr) {
	if (isResponseSuccess(responseJSON)) {
		displayAlert(false, responseJSON[1]);
	}
    else {
		newErrors = ["Youtube failed to save"];
        displayAlert(true, newErrors.concat(responseJSON[1]));
    }
}

function getNavContent(navItemId, navItemType, subNavPrefix) {
	if (navItemType == "SECTION") {
		return $("#" + subNavPrefix + "nav-item-section-" + navItemId).val();
	}
	else if (navItemType == "LINK") {
		return $("#" + subNavPrefix + "nav-item-link-" + navItemId).val();
	}
	else if (navItemType == "DROPDOWN") {
		return '';
	}
}

function addCutDownNavForm(container, navItem, subItem, change) {
	var subNavPrefix = subItem ? "sub-" : "";

	// create title input items
	var newTitleInputDiv = $('<div />').attr({
		class: "input-group"
	});
	
	var newTitleInputSpan = $('<span />').attr({
		class: "input-group-addon"
	});
	newTitleInputSpan.append("Title");
	
	var newTitleInput = $('<input aria-describedby="basic-addon1">').attr({
		id: subNavPrefix + "nav-item-title-" + navItem.id,
		type: "text",
		class: "form-control",
		value: navItem.text
	});
	newTitleInputDiv.append(newTitleInputSpan).append(newTitleInput);
	
	// create type select
	var newTypeInputDiv = $('<div />').attr({
		class: "input-group"
	});
	
	var newTypeInputSpan = $('<span />').attr({
		class: "input-group-addon"
	});
	newTypeInputSpan.append("Type");
	
	newTypeInput = $('<select aria-describedby="basic-addon1">').attr({
		id: subNavPrefix + "nav-item-type-" + navItem.id,
		class: "form-control"
	});
	
	if (change) {
		newTypeInput.bind('change', { subNavPrefix: subNavPrefix, navItemId: navItem.id }, changeNavType);
	}
	
	$("<option>").val("SECTION").text("Section").prop("selected", navItem.type == "SECTION").appendTo(newTypeInput);
	$("<option>").val("LINK").text("Link").prop("selected", navItem.type == "LINK").appendTo(newTypeInput);
	if (!subItem) {
		$("<option>").val("DROPDOWN").text("Dropdown").prop("selected", navItem.type == "DROPDOWN").appendTo(newTypeInput);
	}
	
	newTypeInputDiv.append(newTypeInputSpan).append(newTypeInput);
	container.append(newTitleInputDiv).append(newTypeInputDiv);
}

function addNavForm(container, navItem, subItem) {
	var subNavPrefix = subItem ? "sub-" : "";
	
	addCutDownNavForm(container, navItem, subItem, true);
	
	// create section select
	var newSectionInputDiv = $('<div />').attr({
		id: subNavPrefix + "nav-item-section-div-" + navItem.id,
		class: "input-group"
	});
	
	if (navItem.type != "SECTION") {
		newSectionInputDiv.css("display", "none");
	}
	
	var newSectionInputSpan = $('<span />').attr({
		class: "input-group-addon"
	});
	newSectionInputSpan.append("Section");
	
	newSectionInput = $('<select aria-describedby="basic-addon1">').attr({
		id: subNavPrefix + "nav-item-section-" + navItem.id,
		class: "form-control"
	});
	
	for (var j = 0; j < sections.length; j++) {
		var section = sections[j];
		$("<option>").val(section.id).text(section.title).prop("selected", navItem.sectionId == section.id).appendTo(newSectionInput);
	}
	
	newSectionInputDiv.append(newSectionInputSpan).append(newSectionInput);
	
	// create link item. May, or may not be visible. depends on the above select.
	var newLinkInputDiv = $('<div />').attr({
		id: subNavPrefix + "nav-item-link-div-" + navItem.id,
		class: "input-group"
	});
	
	if (navItem.type != "LINK") {
		newLinkInputDiv.css("display", "none");
	}
	
	var newLinkInputSpan = $('<span />').attr({
		class: "input-group-addon"
	});
	newLinkInputSpan.append("Link");
	
	newLinkInput = $('<input aria-describedby="basic-addon1">').attr({
		id: subNavPrefix + "nav-item-link-" + navItem.id,
		type: "text",
		class: "form-control",
		value: navItem.url,
		placeholder: "URL"
	});
	newLinkInputDiv.append(newLinkInputSpan).append(newLinkInput);
	
	container.append(newSectionInputDiv).append(newLinkInputDiv);
	
	if (!subItem) {
		// create sub nav items
		var newDropdownInputDiv = $('<div aria-multiselectable="true" />').attr({
			id: "nav-item-sub-nav-div-" + navItem.id,
			class: "panel-group sub-nav",
			role: "tablist"
		});
	
		if (navItem.type != "DROPDOWN") {
			newDropdownInputDiv.css("display", "none");
		}
		
		
		var subNavList = $("<ul aria-multiselectable='true' />").attr({
			id: "nav-accordion-" + navItem.id,
			class: "panel-group",
			role: "tablist"
		});
		subNavList.empty();
		
		var addButton = $("<button/>").attr({
			class: "btn btn-primary btn-add"
		});
		addButton.append("Add Sub Navigation Item");
		addButton.bind('click', { parentId: navItem.id }, addNavItemClickHandler);
		subNavList.append(addButton);
		if (navItem.subNavItems) {
			for (var i = 0; i < navItem.subNavItems.length; i++) {
				buildNavListItem(navItem.subNavItems[i], subNavList, navItem.id);
			}
		}
		
		newDropdownInputDiv.append(subNavList);
		
		Sortable.create(subNavList[0], {
			handle: '.sub-orderable-handle',
			animation: 150,
			store: {
				get: function (sortable) {
					return [];
				},
				set: function (sortable) {
					var order = {order: sortable.toArray()};
					$.post("service.php?save=1&subnav=1", order, null, "json").fail(catchAjaxError);
				}
			}
		});

		container.append(newDropdownInputDiv);
	}
}

function buildNavListItem(navItem, navList, parentId) {
	var subNavPrefix = parentId ? "sub-" : "";
	
	/* create header items */
	var handle = $("<span />").attr({
		class: subNavPrefix + "orderable-handle glyphicon glyphicon-resize-vertical"
	});
	
	// title link on the collapsed div
	var newAnchor = $("<a data-toggle='collapse' data-parent='#" + navList.attr('id') + "' aria-expanded='false' aria-controls='" + subNavPrefix + "nav-collapse-" + navItem.id + "' />").attr({
		id: subNavPrefix + "nav-item-header-" + navItem.id,
		class: "collapsed",
		role: "button",
		href: "#" + subNavPrefix + "nav-collapse-" + navItem.id
	});
	newAnchor.append(navItem.text);
	
	// heading the anchor tag is inside of
	var newHeader = $('<h4 />').attr({
		class: "panel-title"
	});
	newHeader.append(handle).append(newAnchor);
	
	// enclosing header div, bind click action so user is not required to click link, but can click the entire header div
	var newHeaderDiv = $("<div />").attr({
		id: subNavPrefix + "nav-heading-" + navItem.id,
		class: "panel-heading",
		role: "tab"
	});
	newHeaderDiv.append(newHeader);
	newHeaderDiv.bind('click ontouchstart', { elementId: "#" + subNavPrefix + "nav-collapse-" + navItem.id, parentId: "#" + navList.attr('id') }, toggleElementCollapse);
	
	/* create body items */
	
	// body div to contain the input items
	var newBodyDiv = $('<div />').attr({
		class: "panel-body"
	});
	
	addNavForm(newBodyDiv, navItem, parentId);
	
	var saveButton = $("<button />").attr({
		id: subNavPrefix + "nav-save-" + navItem.id,
		class: "btn btn-success btn-left"
	});
	saveButton.bind('click', { subNav: (parentId ? true : false), navItemId: navItem.id }, saveNavItem);
	
	var saveSpan = $("<span />").attr({
		class: "glyphicon glyphicon-ok"
	});
	
	saveButton.append(saveSpan).append(" Save");
	newBodyDiv.append(saveButton);
	
	var deleteButton = $("<button />").attr({
		id: subNavPrefix + "nav-delete-" + navItem.id,
		class: "btn btn-danger btn-right"
	});
	deleteButton.bind('click', { subNav: parentId, navItemId: navItem.id }, deleteNavItem);
	
	var deleteSpan = $("<span />").attr({
		class: "glyphicon glyphicon-remove"
	});
	
	deleteButton.append(deleteSpan).append(" Delete");
	newBodyDiv.append(deleteButton);
	
	var collapseDiv = $("<div aria-labelledby='" + subNavPrefix + "nav-heading-" + navItem.id + "' />").attr({
		id: subNavPrefix + "nav-collapse-" + navItem.id,
		class: "panel-collapse collapse",
		role: "tabpanel"
	});
	collapseDiv.append(newBodyDiv);

	var newListItem = $("<li data-id='" + navItem.id + "' data-name='" + navItem.text + "' />").attr({
		id: subNavPrefix + "nav-li-" + navItem.id,
		class: "panel panel-default"
	});
	newListItem.append(newHeaderDiv).append(collapseDiv);
	
	navList.append(newListItem);
}

function addNavItemClickHandler(event) {
	var parentId = event.data.parentId;
	
	var navItem = { id: 0, type: "SECTION", text: "" }; //fake nav object, needed for population function
	
	var label = $('#addItemLabel');
	label.empty();
	label.append("Add New " + (parentId ? "Sub " : "") + "Navigation Item");
	
	var body = $('#addItemBody');
	body.empty();
	addCutDownNavForm(body, navItem, parentId);
	
	var saveButton = $('#addItemSaveButton');
    saveButton.prop("disabled", false);
	saveButton.unbind('click');
	saveButton.bind('click', { parentId: parentId }, addNavItemSaveClickHandler);
	
	$('#addItemModal').modal('show');
}

function addSectionClickHandler(event) {	
	var label = $('#addItemLabel');
	label.empty();
	label.append("Add New Section");
	
	var section = { id: 0, type: "TEXT", title: "" };
	
	var body = $('#addItemBody');
	body.empty();
	addSectionTitleAndTypeForm(body, section);
	
	var saveButton = $('#addItemSaveButton');
    saveButton.prop("disabled", false);
	saveButton.unbind('click');
	saveButton.bind('click', {}, addSectionSaveClickHandler);
	
	$('#addItemModal').modal('show');
}

function addYoutubeClickHandler(event) {	
	var data = event.data;
	var sectionId = data.sectionId;
	
	var label = $('#addItemLabel');
	label.empty();
	label.append("Add New Youtube Channel");
	
	var youtube = { id: 0, channelName: "", channelUrl: ""};
	
	var saveButton = $('#addItemSaveButton');
    saveButton.prop("disabled", false);
	saveButton.unbind('click');
	saveButton.bind('click', { sectionId: sectionId }, addYoutubeSaveClickHandler);
	
	var body = $('#addItemBody');
	body.empty();
	addYoutubeForm(body, youtube);
	
	$('#addItemModal').modal('show');
}

function addImageClickHandler(event) {
	var data = event.data;
	var sectionId = data.sectionId;
	
	var label = $('#addItemLabel');
	label.empty();
	label.append("Add New Image");
	
	var image = { id: 0, title: "", filePath: ""};
	
	var saveButton = $('#addItemSaveButton');
    saveButton.prop("disabled", false);
	saveButton.unbind('click');
	saveButton.bind('click', { saveButton: saveButton, imageId: 0, sectionId: sectionId }, imageSaveClickHandler);
	
	var body = $('#addItemBody');
	body.empty();
	addImageForm(body, image, saveButton);
	
	$('#addItemModal').modal('show');
}

function updateLabel(event) {
	var data = event.data;
	var input = $('#section-image-file-' + data.imageId);
	var label = $('#section-image-file-path-' + data.imageId);
	var path = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	label.val(path);
}

function uploadHandler(event) {
	var data = event.data;
	data.saveButton.prop("disabled", true);
	var input = $('#section-image-file-' + data.imageId);
	var label = $('#section-image-file-path-' + data.imageId);
	var progress = $('#section-progress-' + data.imageId);
	var progressBar = $('#section-progress-bar-' + data.imageId);
	var saveButton = data.saveButton;
	var callback = data.callback;
	var file = input[0].files[0];
	
	if (file) {
		progress.slideDown("fast");
		progressBar.css("width", "0");
		
		var path = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		label.val(path);
		
		var formData = new FormData();
		formData.append("image", file);
		
		$.ajax({
			url: 'service.php?file=0',
			type: 'POST',
			xhr: function() {
				var myXhr = $.ajaxSettings.xhr();
				if(myXhr.upload){
					myXhr.upload.addEventListener('progress',
						function(event) {
							if (event.lengthComputable){
								progressBar.attr("aria-valuemax", event.total).attr("aria-valuenow", event.loaded).css("width", ((event.loaded / event.total) * 100) + "%");
							}
						},
						false);
				}
				return myXhr;
			},

			data: formData,
			dataType: "json",
			success: function(responseJSON, textStatus, xhr ) {			
				progress.slideUp("fast", function(){progressBar.css("width", 0);});
				if (isResponseSuccess(responseJSON)) {
					label.attr("class", "form-control file-path-success");
					label.attr("status", "success");
					label.val(responseJSON[1]);
					event.data = { sectionId: data.sectionId, imageId: data.imageId, saveButton: data.saveButton };
					callback(event);
				}
				else {
					label.attr("class", "form-control file-path-error");
					label.attr("status", "errored");
					label.val(responseJSON[1]);
					saveButton.prop("disabled", false);
				}
			},
			error: catchAjaxError,
			cache: false,
			contentType: false,
			processData: false
		});
	}
	else {
		event.data = { sectionId: data.sectionId, imageId: data.imageId, saveButton: data.saveButton };
		callback(event);
	}
}

function addNavItemSaveClickHandler(event) {
    $('#addItemSaveButton').prop("disabled", true);

    var parentId = event.data.parentId;
	var subNavPrefix = parentId ? "sub-" : "";
	var type = $("#" + subNavPrefix + "nav-item-type-0").val();
	var navInfo = {
		title: $("#" + subNavPrefix + "nav-item-title-0").val(),
		type: type,
		parentId: parentId
	};

	$.post("service.php?save=" + (parentId ? "3" : "2"), navInfo, addNewNavItem, "json").fail(catchAjaxError);
	
	$('#addItemModal').modal('hide');
}

function addSectionSaveClickHandler(event) {
    $('#addItemSaveButton').prop("disabled", true);
	var section = { title: $("#section-title-0").val(), type: $("#section-type-0").val() };
	$.post("service.php?save=10", section, addNewSection, "json").fail(catchAjaxError);
	$('#addItemModal').modal('hide');
}

function addYoutubeSaveClickHandler(event) {
    $('#addItemSaveButton').prop("disabled", true);
	
	var youtube = {
		sectionId: event.data.sectionId,
		channelName: $('#section-youtube-name-0').val(),
		channelUrl: $('#section-youtube-url-0').val()
	};

	$.post("service.php?save=11", youtube, addNewYoutube, "json").fail(catchAjaxError);
	
	$('#addItemModal').modal('hide');
}

function imageSaveClickHandler(event) {
	var data = event.data;
	data.saveButton.prop("disabled", true);
	var input = $('#section-image-file-' + data.imageId);
	var label = $('#section-image-file-path-' + data.imageId);
	var progress = $('#section-progress-' + data.imageId);
	var progressBar = $('#section-progress-bar-' + data.imageId);
	var file = input[0].files[0];

	if (file) {
		progress.slideDown("fast");
		progressBar.css("width", "0");
		
		var path = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		label.val(path);
	}
	
	var formData = new FormData();
	formData.append("image", file);
	formData.append("imageId", data.imageId);
	formData.append("sectionId", data.sectionId);
	formData.append("title", $('#section-image-title-0').val());
	
	$.ajax({
		url: 'service.php?save=' + 9,
		type: 'POST',
		xhr: function() {
			var myXhr = $.ajaxSettings.xhr();
			if(myXhr.upload){
				myXhr.upload.addEventListener('progress',
					function(event) {
						if (event.lengthComputable){
							progressBar.attr("aria-valuemax", event.total).attr("aria-valuenow", event.loaded).css("width", ((event.loaded / event.total) * 100) + "%");
						}
					},
					false);
			}
			return myXhr;
		},
		data: formData,
		dataType: "json",
		success: function(responseJSON, textStatus, xhr ) {			
			progress.slideUp("fast", function(){progressBar.css("width", 0);});
			if (isResponseSuccess(responseJSON)) {
				label.attr("class", "form-control file-path-success");
				label.attr("status", "success");
				
				if (data.imageId > 0) {
					displayAlert(false, responseJSON[1]);
				}
				else {
					buildImageListItem(responseJSON[1], $('#section-images-' + responseJSON[1].sectionId), responseJSON[1].sectionId);
					displayAlert(false, ["Image added"]);
				}
			}
			else {
				if (responseJSON[0] == "INLINE") {
					label.attr("class", "form-control file-path-error");
					label.attr("status", "errored");
					label.val(responseJSON[1]);
				}
				else {
					displayAlert(true, ["Failed to save image", responseJSON[1]]);
				}
			}
		},
		error: catchAjaxError,
		cache: false,
		contentType: false,
		processData: false
	});
	
	data.saveButton.prop("disabled", false);
	if (data.imageId == 0) {
		$('#addItemModal').modal('hide');
	}
}

function addNewNavItem(responseJSON, textStatus, xhr) {
	if (isResponseSuccess(responseJSON)) {
		var navItem = responseJSON[1];
		parent = navItem.parentId ? $("#nav-accordion-" + navItem.parentId) : $("#nav-accordion");
		buildNavListItem(navItem, parent, navItem.parentId);
		displayAlert(false, [(navItem.parentId ? "Sub " : "") + "Navigation item added"]);
	}
	else {
		displayAlert(true, ["Failed to add navigation item", responseJSON[1]]);
	}
}

function addNewSection(responseJSON, textStatus, xhr) {
	if (isResponseSuccess(responseJSON)) {
		displayAlert(false, ["Section added"]);
		loadAll();
	}
	else {
		displayAlert(true, ["Failed to add section", responseJSON[1]]);
	}
}

function addNewYoutube(responseJSON, textStatus, xhr) {
	if (isResponseSuccess(responseJSON)) {
		buildYoutubeListItem(responseJSON[1], $('#section-youtubes-' + responseJSON[1].sectionId), responseJSON[1].sectionId);
		displayAlert(false, ["Youtube channel added"]);
	}
	else {
		displayAlert(true, ["Failed to add youtube channel", responseJSON[1]]);
	}
}

function addSectionTitleAndTypeForm(body, section, change) {
	// create title input items
	var newTitleInputDiv = $('<div />').attr({
		class: "input-group"
	});
	
	var newTitleInputSpan = $('<span />').attr({
		class: "input-group-addon"
	});
	newTitleInputSpan.append("Title");
	
	newTitleInput = $('<input aria-describedby="basic-addon1">').attr({
		id: "section-title-" + section.id,
		type: "text",
		class: "form-control",
		value: section.title
	});
	newTitleInputDiv.append(newTitleInputSpan).append(newTitleInput);
	body.append(newTitleInputDiv);
	
	// create type select
	var newTypeInputDiv = $('<div />').attr({
		class: "input-group"
	});
	
	var newTypeInputSpan = $('<span />').attr({
		class: "input-group-addon"
	});
	newTypeInputSpan.append("Type");
	
	newTypeInput = $('<select aria-describedby="basic-addon1">').attr({
		id: "section-type-" + section.id,
		class: "form-control"
	});
	
	if (change) {
		newTypeInput.bind('change', { sectionId: section.id }, changeSectionType);
	}
	
	$("<option>").val("INTRO").text("Introduction").prop("selected", section.type == "INTRO").appendTo(newTypeInput);
	$("<option>").val("TEXT").text("Text").prop("selected", section.type == "TEXT").appendTo(newTypeInput);
	$("<option>").val("MEDIA").text("Media").prop("selected", section.type == "MEDIA").appendTo(newTypeInput);
	$("<option>").val("JOIN").text("Recruitment").prop("selected", section.type == "JOIN").appendTo(newTypeInput);
	$("<option>").val("MODS").text("List of Modifications").prop("selected", section.type == "MODS").appendTo(newTypeInput);
	
	newTypeInputDiv.append(newTypeInputSpan).append(newTypeInput);
	body.append(newTypeInputDiv);
}

function addImageForm(body, image, saveButton) {
	// create image title input items
	var imgTitleInputDiv = $('<div />').attr({
		class: "input-group"
	});
	
	var imgTitleInputSpan = $('<span />').attr({
		class: "input-group-addon"
	});
	imgTitleInputSpan.append("Image Title");
	
	imgTitleInput = $('<input aria-describedby="basic-addon1">').attr({
		id: "section-image-title-" + image.id,
		type: "text",
		class: "form-control",
		value: image.title
	});
	imgTitleInputDiv.append(imgTitleInputSpan).append(imgTitleInput);
	body.append(imgTitleInputDiv);
	
	// create image file input items
	var imgFileInputDiv = $('<div />').attr({
		class: "input-group"
	});
	
	var imgBrowseSpan = $('<span />').attr({
		class: "input-group-btn"
	});
	
	var imgBrowseButtonSpan = $('<span />').attr({
		class: "btn btn-primary btn-file"
	});

	var imgBrowseInput = $('<input>').attr({
		id: "section-image-file-" + image.id,
		type: "file",
		accept: "image/jpeg,image/png,image/bmp"
	});
	
	imgBrowseInput.change({ imageId: image.id }, updateLabel);
	imgBrowseButtonSpan.append("Browse&hellip;").append(imgBrowseInput);
	imgBrowseSpan.append(imgBrowseButtonSpan);
	
	var imgBrowseText = $('<input aria-describedby="basic-addon1">').attr({
		class: "form-control file-path",
		id: "section-image-file-path-" + image.id,
		type: "text",
		value: image.filePath
	});
	imgBrowseText.prop("readonly", true);
	
	imgFileInputDiv.append(imgBrowseSpan).append(imgBrowseText);
	
	var progress = $('<div />').attr({
		id: "section-progress-" + image.id,
		class: "progress file-progress"
	});
	progress.css("display", "none");
	
	var progressBar = $('<div aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"/>').attr({
		id: "section-progress-bar-" + image.id,
		class: "progress-bar",
		role: "progressbar"
	});
	progress.append(progressBar);

	body.append(imgFileInputDiv).append(progress);
}

function buildImageListItem(image, imageList, sectionId) {
	var imgHandle = $("<span />").attr({
		class: "img-orderable-handle glyphicon glyphicon-resize-vertical"
	});
	
	var imgRemove = $("<span />").attr({
		class: "img-remove glyphicon glyphicon-remove"
	});
	imgRemove.bind('click', { imageId: image.id }, deleteImage);
	
	//create header items
	var imgAnchor = $("<a data-toggle='collapse' data-parent='#section-images-" +  sectionId + "' aria-expanded='false' aria-controls='section-image-collapse-" + image.id + "' \>").attr({
		id: "img-title-" + image.id,
		class: "collapsed",
		role: "button",
		href: "#section-image-collapse-" + image.id
	});
	imgAnchor.append(image.title);
	
	var imgHeader = $('<h4 />').attr({
		class: "panel-title"
	});
	imgHeader.append(imgHandle).append(imgAnchor).append(imgRemove);
	
	var imgHeaderDiv = $('<div />').attr({
		class: "panel-heading",
		role: "tab",
		id: "section-image-heading-" + image.id
	});
	imgHeaderDiv.append(imgHeader);
	imgHeaderDiv.bind('click', { elementId: "#section-image-collapse-" + image.id, parentId: "#section-images-" +  sectionId }, toggleElementCollapse); 
	
	//create body items
	var imgBodyDiv = $('<div />').attr({
		class: "panel-body"
	});
	
	var imgSaveButton = $("<button />").attr({
		id: "img-save-" + image.id,
		class: "btn btn-success btn-left"
	});
	imgSaveButton.bind('click', { imageId: image.id, saveButton: imgSaveButton, sectionId: section.id }, imageSaveClickHandler);
	
	addImageForm(imgBodyDiv, image, imgSaveButton);

	var saveSpan = $("<span />").attr({
		class: "glyphicon glyphicon-ok"
	});
	
	imgSaveButton.append(saveSpan).append(" Save");

	var imgDeleteButton = $("<button />").attr({
		id: "img-delete-" + image.id,
		class: "btn btn-danger btn-right"
	});
	imgDeleteButton.bind('click', { imageId: image.id }, deleteImage);
	
	var imgDeleteSpan = $("<span />").attr({
		class: "glyphicon glyphicon-remove"
	});
	
	imgDeleteButton.append(imgDeleteSpan).append(" Delete");
	
	imgBodyDiv.append(imgSaveButton).append(imgDeleteButton);
	
	var imgCollapseDiv = $("<div aria-labelledby='section-image-heading-" + image.id + "' />").attr({
		id: "section-image-collapse-" + image.id,
		class: "panel-collapse collapse",
		role: "tabpanel"
	});
	imgCollapseDiv.append(imgBodyDiv);

	var imgListItem = $("<li data-id='" + image.id + "' data-name='" + image.title + "'></li>").attr({
		id: "section-image-li-" + image.id,
		class: "panel panel-default"
	});
	imgListItem.append(imgHeaderDiv).append(imgCollapseDiv);
	imageList.append(imgListItem);
}

function addYoutubeForm(body, youtube) {
	// create channel name input items
	var ytChannelNameInputDiv = $('<div />').attr({
		class: "input-group"
	});
	
	var ytChannelNameInputSpan = $('<span />').attr({
		class: "input-group-addon"
	});
	ytChannelNameInputSpan.append("Channel Name");
	
	ytChannelNameInput = $('<input aria-describedby="basic-addon1">').attr({
		id: "section-youtube-name-" + youtube.id,
		type: "text",
		class: "form-control",
		value: youtube.channelName
	});
	ytChannelNameInputDiv.append(ytChannelNameInputSpan).append(ytChannelNameInput);
	body.append(ytChannelNameInputDiv);
	
	// create channel url input items
	var ytChannelUrlInputDiv = $('<div />').attr({
		class: "input-group"
	});
	
	var ytChannelUrlInputSpan = $('<span />').attr({
		class: "input-group-addon"
	});
	ytChannelUrlInputSpan.append("Channel URL");
	
	ytChannelUrlInput = $('<input aria-describedby="basic-addon1">').attr({
		id: "section-youtube-url-" + youtube.id,
		type: "text",
		class: "form-control",
		value: youtube.channelUrl
	});
	ytChannelUrlInputDiv.append(ytChannelUrlInputSpan).append(ytChannelUrlInput);
	body.append(ytChannelUrlInputDiv);
}

function buildYoutubeListItem(youtube, youtubeList, sectionId) {
	var ytHandle = $("<span />").attr({
		class: "yt-orderable-handle glyphicon glyphicon-resize-vertical"
	});
	
	var ytRemove = $("<span />").attr({
		class: "yt-remove glyphicon glyphicon-remove"
	});
	ytRemove.bind('click', { youtubeId: youtube.id }, deleteYoutube);
	
	//create header items
	var ytAnchor = $("<a data-toggle='collapse' data-parent='#section-youtubes-" +  sectionId + "' aria-expanded='false' aria-controls='section-youtube-collapse-" + youtube.id + "' \>").attr({
		id: "yt-title-" + youtube.id,
		class: "collapsed",
		role: "button",
		href: "#section-youtube-collapse-" + youtube.id
	});
	ytAnchor.append(youtube.channelName);
	
	var ytHeader = $('<h4 />').attr({
		class: "panel-title"
	});
	ytHeader.append(ytHandle).append(ytAnchor).append(ytRemove);
	
	var ytHeaderDiv = $('<div />').attr({
		class: "panel-heading",
		role: "tab",
		id: "section-youtube-heading-" + youtube.id
	});
	ytHeaderDiv.append(ytHeader);
	ytHeaderDiv.bind('click', { elementId: "#section-youtube-collapse-" + youtube.id, parentId: "#section-youtubes-" +  sectionId }, toggleElementCollapse); 
	
	//create body items
	var ytBodyDiv = $('<div />').attr({
		class: "panel-body"
	});
	
	addYoutubeForm(ytBodyDiv, youtube)
	
	var saveButton = $("<button />").attr({
		id: "yt-save-" + youtube.id,
		class: "btn btn-success btn-left"
	});
	saveButton.bind('click', { youtubeId: youtube.id }, saveYoutube);
	
	var saveSpan = $("<span />").attr({
		class: "glyphicon glyphicon-ok"
	});
	
	saveButton.append(saveSpan).append(" Save");

	var deleteButton = $("<button />").attr({
		id: "yt-delete-" + youtube.id,
		class: "btn btn-danger btn-right"
	});
	deleteButton.bind('click', { youtubeId: youtube.id }, deleteYoutube);
	
	var deleteSpan = $("<span />").attr({
		class: "glyphicon glyphicon-remove"
	});
	
	deleteButton.append(deleteSpan).append(" Delete");
	
	ytBodyDiv.append(saveButton).append(deleteButton);
	
	var ytCollapseDiv = $("<div aria-labelledby='section-youtube-heading-" + youtube.id + "' />").attr({
		id: "section-youtube-collapse-" + youtube.id,
		class: "panel-collapse collapse",
		role: "tabpanel"
	});
	ytCollapseDiv.append(ytBodyDiv);

	var ytListItem = $("<li data-id='" + youtube.id + "' data-name='" + youtube.channelName + "'></li>").attr({
		id: "section-youtube-li-" + youtube.id,
		class: "panel panel-default"
	});
	ytListItem.append(ytHeaderDiv).append(ytCollapseDiv);
	youtubeList.append(ytListItem);
}

function deleteNavItem(event) {
	var data = event.data;
    $.post("service.php?del=" + (data.subNav ? "1" : "0"), {id: data.navItemId}, handleDeletedNavItem, "json").fail(catchAjaxError);
	
	fadeAndRemove($("#" + (data.subNav ? "sub-" : "") + "nav-li-" + data.navItemId));
}

function deleteSection(event) {
	var data = event.data;
    setTimeout(function() {$.post("service.php?del=4", {id: data.sectionId}, handleDeletedSection, "json").fail(catchAjaxError);}, 500);
}

function deleteImage(event) {
	var data = event.data;
    $.post("service.php?del=3", {id: data.imageId}, handleDeletedSectionItem, "json").fail(catchAjaxError);
	
	fadeAndRemove($("#section-image-li-" + data.imageId));
}

function deleteYoutube(event) {
	var data = event.data;
    $.post("service.php?del=2", {id: data.youtubeId}, handleDeletedSectionItem, "json").fail(catchAjaxError);
	
	fadeAndRemove($("#section-youtube-li-" + data.youtubeId));
}

function handleDeletedSection(responseJSON, status, xhr) {
	displayAlert(!isResponseSuccess(responseJSON[0]), responseJSON[1]);
	if (isResponseSuccess(responseJSON[0])) {
		loadAll();
	}
}	

function handleDeletedSectionItem(responseJSON, status, xhr) {
	displayAlert(!isResponseSuccess(responseJSON[0]), responseJSON[1]);
}

function handleDeletedNavItem(responseJSON, status, xhr) {
	displayAlert(!isResponseSuccess(responseJSON[0]), responseJSON[1]);
}
