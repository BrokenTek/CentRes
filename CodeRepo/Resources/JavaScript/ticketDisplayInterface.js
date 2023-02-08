function setTicketDisplayVariable(variableName, value, id = 'ticketContainer') {
    var ticketContainer = document.getElementById(id);
    var ticketForm = ticketContainer.contentWindow.document.getElementById('ticketForm');
    var variableElement = ticketContainer.contentWindow.document.getElementById(variableName);

    if (variableElement != null) {
        variableElement.remove();
    }

    variableElement = document.createElement('input');
    variableElement.setAttribute('type', 'hidden');
    variableElement.setAttribute('class', 'variable');
    variableElement.setAttribute('id', variableName);
    variableElement.setAttribute('name', variableName);
    variableElement.setAttribute('value', value);

    ticketForm.appendChild(variableElement);

    updateTicketDisplay();
}

function removeTicketDisplayVariable(variableName, value, id = 'ticketContainer') {
    var ticketContainer = document.getElementById(id);
    var ticketForm = ticketContainer.contentWindow.document.getElementById('ticketForm');
    var variableElement = ticketContainer.contentWindow.document.getElementById(variableName);

    if (variableElement != null) {
        variableElement.remove();
    }
}

function clearTicketDisplayVariables(id = 'ticketContainer') {
    var ticketContainer = document.getElementById(id);
    var ticketForm = ticketContainer.contentWindow.document.getElementById('ticketForm');
    var vars = ticketForm.getElementsByClassName('variable');
    for (var i = vars.length - 1; i >= 0; i--) {
        vars[i].remove();
    }
}

function updateTicketDisplay(id = 'ticketContainer') {
    
    var ticketContainer = document.getElementById(id);
    var ticketForm = ticketContainer.contentWindow.document.getElementById('ticketForm');
    ticketForm.submit();
    checkIframeLoaded(id);
}

function checkIframeLoaded(id = 'ticketContainer') {
    // Get a handle to the iframe element
    var iframe = document.getElementById(id);
    var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

    // Check if loading is complete
    if (  iframeDoc.readyState  == 'complete' ) {
        //iframe.contentWindow.alert("Hello");
        iframe.contentWindow.onload = function(){
            alert("I am loaded");
        };
        // The loading is complete, call the function we want executed once the iframe is loaded
        afterLoading();
        return;
    } 

    // If we are here, it is not loaded. Set things up so we check   the status again in 100 milliseconds
    window.setTimeout(checkIframeLoaded, 100);
}

function afterLoading(){
    //alert("I am here");
}