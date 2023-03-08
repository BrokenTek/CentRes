
function setVar(variableName, value, id = null, update = false) {
    if (value == null || value === undefined) {
        if (getVar(variableName, id) === undefined) {
            return false;
        }
        return removeVar(variableName, id, update);
    }
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
            return undefined;
        }
    }

    if (variableElement != null) {
        if (variableElement.getAttribute('value') == value) {
            return false;
        }
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
    return true;
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

function getVarOnce(variableName, id = null, update = false) {
    let val = getVar(variableName, id);
    if (val !== undefined) {
        removeVar(variableName, id);
        if (update) {
            updateDisplay(id);
        }
    }
    return val;
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
                return undefined;
            }
        }
        catch (err) {
            return  undefined;
        }
    }
    if (variableElement != null) {
        variableName;
        variableElement.remove();
    }
    else {
        return false;
    }
    if (update) {
        updateDisplay(id);
    }
    return true;
}

function renameVar(oldVarName, newVarName, id = null, update = false) {
    var container = document.getElementById(id);
    var form;
    var variableElement;
    if (id == null) {
        form = document.getElementsByTagName('form')[0];
        variableElement = document.getElementById(oldVarName);
    }
    else {
        form = container.contentWindow.document.getElementsByTagName('form')[0];
        variableElement = container.contentWindow.document.getElementById(oldVarName);
        try {
            if (form == null) {
                setTimeout(renameVar(oldVarName, newVarName, id, update, update), 250);
                return;
            }
        }
        catch (err) {
            return;
        }
    }
    if (variableElement != null) {
        variableElement.id = newVarName;
        variableElement.setAttribute("name",newVarName);
    }
    if (update) {
        updateDisplay(id);
    }
}

function varCpy(variableName, source = null, destination = null, updateDestination = false, allowUndefinedVariables = false) {
    let val = getVar(variableName, source);
    let val2 = getVar(variableName, destination);
    if (val === val2 || (val === undefined && !allowUndefinedVariables)) {
        return false;
    }
    return setVar(variableName, val, destination, updateDestination);
}

function varCpyRen(sourceVariableName, source = null, destinationVariableName, destination = null, updateDestination, allowUndefinedVariables = false) {
    let val = getVar(sourceVariableName, source);
    let val2 = getVar(destinationVariableName, destination);
    if (sourceVariableName == "tableList" && val === undefined) {
        //alert(val2);
    }
    if (val === val2 || (val === undefined && !allowUndefinedVariables)) {
        return false;
    }
    return setVar(destinationVariableName, val, destination, updateDestination);
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

function toggleSortKey(tableId, columnName, refresh = true) {
    if (document.getElementById(tableId) == null) {
        throw("toggleSortKey Error! Table doesn't exist");
    }
    let keyIndex = 1;
    let keyFound = false;
    let offsetBy1 = false;
    let sortKeyPrefix = tableId + "_SortKey";
    while (true) {
        let value = getVar(sortKeyPrefix + keyIndex);

        // end of key list
        if (value === undefined) {
            break;
        }

        // key was removed. All keys to right need to be left-shifted by 1
        if (offsetBy1) {
            renameVar(sortKeyPrefix + keyIndex, sortKeyPrefix + (keyIndex - 1));
            keyIndex++;
            continue;
        }

        // if key is the column you specified
        let lookAt = value.replace(" ASC","");
        lookAt = lookAt.replace(" DESC","");
        if (lookAt == columnName) {
           
            keyFound = true;

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

function clearSortKeys(tableId) {
    let sortKeyPrefix = tableId + "_SortKey";
    let keyIndex = 1;
    while (true) {
        let value = getVar(sortKeyPrefix + keyIndex);

        // end of key list
        if (value === undefined) {
            break;
        }
        removeVar(sortKeyPrefix + keyIndex);
        keyIndex ++;
    }
}