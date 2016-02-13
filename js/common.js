function toggleElementCollapse(event) {
	var data = event.data;
	$(data.elementId).collapse( {parent: data.parentId, toggle: false} );
	$(data.elementId).collapse('toggle');
}

function fadeAndRemove(li) {
	li.fadeOut("fast", function() {
		li.remove;
	});
}

function isResponseSuccess(response) {
	return "SUCCESS" == response[0];
}

function catchAjaxError() {
    displayAlert(true, []);
}

function displayAlert(isError, messageArray) {
    var resultContainer = $("#results");
	resultContainer.slideUp("slow");
	resultContainer.empty();
    
    resultContainer.attr({
        class: "padded text-center alert alert-dismissible " + (isError ? "alert-danger" : "alert-success")
    });
	
	var closeButton = $("<button />").attr({
		type: "button",
		class: "close"
	});
    
    var closeSpan = $("<span />");
	closeSpan.append("&times;");
	closeButton.append(closeSpan);
    
    var messageDiv = $("<div />").attr({
        class: "message"
    });
    
    for (var i = 0; i < messageArray.length; i++) {
        messageDiv.append("<p>" + messageArray[i] + "</p>");
    }
    
    if (isError) {
        closeButton.bind('click', {}, function() {
            loadAll();
            resultContainer.slideUp("slow");
        });
        messageDiv.append("<p>Error on operation, close to try and reload</p>");
        $('html, body').animate({scrollTop: resultContainer.offset().top - 70 }, 1000);
    }
    else {
        closeButton.bind('click', {}, function() {resultContainer.slideUp("slow");});
    }
	
	resultContainer.append(closeButton).append(messageDiv);
	resultContainer.slideDown("slow");
}