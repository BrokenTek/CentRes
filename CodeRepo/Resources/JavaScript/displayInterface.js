function setVar(variableName, value, id = null, update = false) {
    var container = document.getElementById(id);
    var form;
    var variableElement;
    if (id == undefined) {
        form = document.getElementsByTagName('form')[0];
        variableElement = document.getElementById(variableName);
    }
    else {
        form = container.contentWindow.document.getElementsByTagName('form')[0];
        variableElement = container.contentWindow.document.getElementById(variableName);

        // the page was unavailable when attempting to set the variable...
        // reprocess the request until successful.
        if (form == null) {
            setTimeout(setVar(variableName), 250);
            return;
        }
    }

    if (variableElement != null) {
        variableElement.setAttribute('value', value);
    }
    else {
        if (id == null){
            variableElement = document.createElement('input');
        }
        else {
            variableElement = container.contentWindow.document.createElement('input');
        }
        
        variableElement.setAttribute('type', 'hidden');
        variableElement.setAttribute('class', 'variable');
        variableElement.setAttribute('id', variableName);
        variableElement.setAttribute('name', variableName);
        variableElement.setAttribute('value', value);
        variableElement.setAttribute('style', 'display: none;');
        form.appendChild(variableElement);
    }

    if (update) {
        updateDisplay(id);
    }
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
        if (form == null) {
            throw "getVar error! Variable unavailable";
        }
    }
    
    if (variableElement == null || variableElement == "null") {
        return undefined;
    }
    else {
        return variableElement.getAttribute("value");
    }
}

function removeVar(variableName, id = null, update = false) {
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
        try {
            if (form == null) {
                setTimeout(removeVar(variableName, id), 250);
                return;
            }
        }
        catch (err) {
            return;
        }
    }
    if (variableElement != null) {
        variableElement.remove();
    }
}



function clearVars(id = null, update = false) {
    var container = document.getElementById(id);
    var form;
    if (id == null) {
        form = document.getElementsByTagName('form')[0];
    }
    else {
        form = container.contentWindow.document.getElementsByTagName('form')[0];
        if (form == null) {
            setTimeout(clearVars(id), 250);
            return;
        }
    }
    var vars = ticketForm.getElementsByClassName('variable');
    for (var i = vars.length - 1; i >= 0; i--) {
        vars[i].remove();
    }

    if (update) {
        updateDisplay(id);
    }
}

function updateDisplay(id = null) {
    var container = document.getElementById(id);
    var form;
    if (id == null) {
        form = document.getElementsByTagName('form')[0];
    }
    else {
        try {
            form = container.contentWindow.document.getElementsByTagName('form')[0];
            if (form == null) {
                setTimeout(updateDisplay(id), 250);
               return;
            }
        }
        catch (err) {
            return;
        }
    }
    form.submit();
}

// =============================== LOCAL WINDOW ONLY FUNCTIONS ============================

window.onscroll = function (e) { 
    if (getVar("scrollX") != null) {
        setVar("scrollX", window.scrollX);
        setVar("scrollY", window.scrollY);
    }   
}

function rememberScrollPosition() {
    setVar("scrollX", window.scrollX);
    setVar("scrollY", window.scrollY); 
}

function forgetScrollPosition() {
    removeVar("scrollX", id);
    removeVar("ScrollY", id);
}

function toggleSortKey(elementId, columnName, refresh = true) {
    if (document.getElementById(elementId) == null) {
        throw("toggleSortKey Error! Element doesn't exist");
    }
    let keyIndex = 1;
    let keyFound = false;
    let offsetBy1 = false;
    let sortKeyPrefix = elementId + "SortKey";
    while (true) {
        let value = getVar(sortKeyPrefix + keyIndex);

        // end of key list
        if (value === undefined) {
            break;
        }

        // key was removed. All keys to right need to be left-shifted by 1
        if (offsetBy1) {
            renameVar(sortKeyPrefix + keyIndex, sortKeyPrefix + (keyIndex - 1));
        }

        // if key is the column you specified
        if (value.replace(" ASC","").replace(" DESC","") == columnName) {
            keyFound == true;

            // toggle to DESC if it's ASC
            if (value == columnName + " ASC") {
                setVar(sortKeyPrefix + keyIndex, columnName + " DESC");
            }
            // remove the key if it's DESC
            else {
                removeVar(sortKeyPrefix + keyIndex);
                // if there are any other keys to the right of this, left shift them by 1
                offsetBy1 = true;
            }
        }
        keyIndex ++;
    }
    // if the key wasn't found, append to the end of key list.
    if (!keyFound) {
        setVar(sortKeyPrefix + keyIndex, columnName + " ASC");
    }
    if (refresh) { 
        updateDisplay();
    }
}