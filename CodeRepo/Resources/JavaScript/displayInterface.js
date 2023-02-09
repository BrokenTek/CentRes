function setDisplayVariable(variableName, value, id = 'container') {
    var container = document.getElementById(id);
    var form = container.contentWindow.document.getElementsByTagName('form')[0];
    var variableElement = container.contentWindow.document.getElementById(variableName);

    if (variableElement != null) {
        variableElement.remove();
    }

    variableElement = document.createElement('input');
    variableElement.setAttribute('type', 'hidden');
    variableElement.setAttribute('class', 'variable');
    variableElement.setAttribute('id', variableName);
    variableElement.setAttribute('name', variableName);
    variableElement.setAttribute('value', value);

    form.appendChild(variableElement);
}

function removeDisplayVariable(variableName, id = 'container') {
    var container = document.getElementById(id);
    var form = container.contentWindow.document.getElementsByTagName('form')[0];
    var variableElement = ticketContainer.contentWindow.document.getElementById(variableName);

    if (variableElement != null) {
        variableElement.remove();
    }
}

function clearDisplayVariables(id = 'container') {
    var container = document.getElementById(id);
    var form = container.contentWindow.document.getElementsByTagName('form')[0];
    var vars = ticketForm.getElementsByClassName('variable');
    for (var i = vars.length - 1; i >= 0; i--) {
        vars[i].remove();
    }
}

function updateDisplay(id = 'container') {
    alert(id);
    var container = document.getElementById(id);
    var form = container.contentWindow.document.getElementsByTagName('form')[0];
    form.submit();
}