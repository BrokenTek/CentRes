function setVar(variableName, value, id = null) {
    var container = document.getElementById(id);
    var form;
    var variableElement;
    if (id == null) {
        form = document.getElementsByTagName('form')[0];
        variableElement = document.getElementById(variableName);
    }
    else {
        form = container.contentWindow.document.getElementsByTagName('form')[0];
        variableElement = container.contentWindow.document.getElementById(variableName);
    }

    if (variableElement != null) {
        variableElement.remove();
    }

   
    variableElement = document.createElement('input');
    variableElement.setAttribute('type', 'hidden');
    variableElement.setAttribute('class', 'variable');
    variableElement.setAttribute('id', variableName);
    variableElement.setAttribute('name', variableName);
    variableElement.setAttribute('value', value);
    variableElement.setAttribute('style', 'display: none;');
    form.appendChild(variableElement);
}

function getVar(variableName, id = null) {
    var container = document.getElementById(id);
    var form;
    var variableElement;
   
    if (id == null) {
        form = document.getElementsByTagName('form')[0];
        variableElement = document.getElementById(variableName);
    }
    else {
        
        form = container.contentWindow.document.getElementsByTagName('form')[0]; 
        variableElement = container.contentWindow.document.getElementById(variableName);
    }
    
    if (variableElement == null || variableElement == "null") {
        return null;
    }
    else {
        return variableElement.getAttribute("value");
    }
}

function removeVar(variableName, id = null) {
    var container = document.getElementById(id);
    var form;
    var variableElement;
    if (id == null) {
        form = document.getElementsByTagName('form')[0];
        variableElement = document.getElementById(variableName);
    }
    else {
        form = container.contentWindow.document.getElementsByTagName('form')[0];
        variableElement = container.contentWindow.document.getElementById(variableName);
    }

    if (variableElement != null) {
        variableElement.remove();
    }
}

function clearVars(id = null) {
    var container = document.getElementById(id);
    var form;
    if (id == null) {
        form = document.getElementsByTagName('form')[0];
    }
    else {
        form = container.contentWindow.document.getElementsByTagName('form')[0];
    }
    var vars = ticketForm.getElementsByClassName('variable');
    for (var i = vars.length - 1; i >= 0; i--) {
        vars[i].remove();
    }
}

function updateDisplay(id = null) {
    var container = document.getElementById(id);
    var form;
    if (id == null) {
        form = document.getElementsByTagName('form')[0];
    }
    else {
        form = container.contentWindow.document.getElementsByTagName('form')[0];
    }
    form.submit();
}

window.onscroll = function (e) { 
    if (getVar("scrollX") != null) {
        setVar("scrollX", window.scrollX);
        setVar("scrollY", window.scrollY);
    }   
}

function rememberScrollPosition(id = null) {
    setVar("scrollX", window.scrollX);
    setVar("scrollY", window.scrollY); 
}

function forgetScrollPosition(id = null) {
    removeVar("scrollX", id);
    removeVar("ScrollY", id);
}