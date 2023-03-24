
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

        // the page was unavailable when attempting to set the variable…
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

// get a var. If its defined, remove the var and return its value.
// If the variable was retrieved, specify you want to update the target display by setting update = true;
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


// returns false if variable doesn't exist
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
                return false;
            }
        }
        catch (err) {
            return  false;
        }
    }
    if (variableElement != null) {
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
                return false;
            }
        }
        catch (err) {
            return false;
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

// get a variable and copy it. Returns true/false if copied. If you want to allow transfereing an undefined variable
// aka deleting it at the destination, set allowUndefinedVariables = true
// If you need to update the destination if a variable is copied, set updateDestination to true.
// NOTE: variable does not get copied if the value at the source and destination are the same.
function varCpy(variableName, source = null, destination = null, updateDestination = false, allowUndefinedVariables = false) {
    let val = getVar(variableName, source);
    let val2 = getVar(variableName, destination);
    if (val === val2 || (val === undefined && !allowUndefinedVariables)) {
        return false;
    }
    return setVar(variableName, val, destination, updateDestination);
}

// see varCpy function commment. Additionally allows to specify a different destination variable name with destinationVariableName
function varCpyRen(sourceVariableName, source = null, destinationVariableName, destination = null, updateDestination, allowUndefinedVariables = false) {
    let val = getVar(sourceVariableName, source);
    let val2 = getVar(destinationVariableName, destination);
    if (val === val2 || (val === undefined && !allowUndefinedVariables)) {
        return false;
    }
    return setVar(destinationVariableName, val, destination, updateDestination);
}

// transfer a variable from source to destination. Specify if you want to update the source and or destination
// allowUndefinedVariables = true will clear the variable at the destination.
function varXfr(variableName, source = null, destination = null, updateSource = false, updateDestination = false, allowUndefinedVariables = false) {
    if (varCpy(variableName, source, destination, updateDestination, allowUndefinedVariables)) {
        return removeVar(variableName, source, updateSource);
    }
    return false;
}

// transfer a variable from source to destination. Specify if you want to update the source and or destination
// allowUndefinedVariables = true will clear the variable at the destination.
function varXfrRen(sourceVariableName, source = null, destinationVariableName, destination = null, updateSource = false, updateDestination = false, allowUndefinedVariables = false) {
    if (varCpyRen(sourceVariableName, source, destinationVariableName, destination, updateDestination, allowUndefinedVariables)) {
        return removeVar(sourceVariableName, source, updateSource);
    }
    return false;
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
            return false;
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
    var btnSubmit;
    if (id == null) {
        form = document.getElementsByTagName('form')[0];
        btnSubmit = document.getElementById("btnSubmit");
        if (btnSubmit !== null) {
          btnSubmit.click();  
        }
        else {
            form.submit();
        }
    }
    else {
        try {
            form = container.contentWindow.document.getElementsByTagName('form')[0];
            btnSubmit = container.contentWindow.document.getElementById("btnSubmit");
            if (form == null && btnSubmit === null) {
               return false;
            }
            else if (btnSubmit !== null) {
                btnSubmit.click();
            }
            else {
                form.submit();
            }
        }
        catch (err) {
            return false;
           
        }
    }
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