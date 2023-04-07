
function varSet(variableName, value, childIframeId = null, update = false, bypassValidationCheck = false) {
    value = coerce(value);
    if (value === undefined) {
        if (coerce(varGet(variableName, childIframeId)) === undefined) {
            return false;
        }
        return varRem(variableName, childIframeId, update);
    }
    var container = document.getElementById(childIframeId);
    var form;
    var variableElement;
    if (childIframeId == undefined) {
        form = document.getElementsByTagName('form')[0];
        variableElement = document.getElementById(variableName);
    }
    else {
        form = container.contentWindow.document.getElementsByTagName('form')[0];
        variableElement = container.contentWindow.document.getElementById(variableName);

        // the page was unavailable when attempting to set the variable...
        // reprocess the request until successful.
        if (form == null) {
            setTimeout(varSet(variableName), 250);
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
        if (childIframeId == null){
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
        updateDisplay(childIframeId, bypassValidationCheck);
    }
    return true;
}

function varGet(variableName, childIframeId = null) {
    var container = document.getElementById(childIframeId);
    var form;
    var variableElement;
   
    if (childIframeId == null) {
        form = document.getElementsByTagName('form')[0];
        variableElement = document.getElementById(variableName);
    }
    else {
        form = container.contentWindow.document.getElementsByTagName('form')[0]; 
        variableElement = container.contentWindow.document.getElementById(variableName);
        if (form == null) {
            throw "varGet error! " + variableName + " was unreachable at " + childIframeId;
        }
    }
    
    if (variableElement === null || variableElement === undefined) {
        return undefined;
    }
    else {
        return coerce(variableElement.getAttribute("value"));
    }
}

// get a var. If its defined, remove the var and return its value.
// If the variable was retrieved, specify you want to update the target display by setting update = true;
function varGetOnce(variableName, childIframeId = null, update = false, bypassValidationCheck = false) {
    let val = varGet(variableName, childIframeId);
    if (val !== undefined) {
        varRem(variableName, childIframeId);
        if (update) {
            updateDisplay(childIframeId, bypassValidationCheck);
        }
    }
    return coerce(val);
}


// returns false if variable doesn't exist
function varRem(variableName, childIframeId = null, update = false, bypassValidationCheck = false) {
    var container = document.getElementById(childIframeId);
    var form;
    var variableElement;
    if (childIframeId == null) {
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
        updateDisplay(childIframeId, bypassValidationCheck);
    }
    return true;
}

function varRen(oldVarName, newVarName, childIframeId = null, update = false, bypassValidationCheck = false) {
    if (varGet(newVarName, childIframeId) !== undefined) {
        return false;
    }
    var container = document.getElementById(childIframeId);
    var form;
    var variableElement;
    if (childIframeId == null) {
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
        updateDisplay(childIframeId, bypassValidationCheck);
    }
    return true;
}

// get a variable and copy it. Returns true/false if copied. If you want to allow transfereing an undefined variable
// aka deleting it at the destination, set allowUndefinedVariables = true
// If you need to update the destination if a variable is copied, set updateDestination to true.
// NOTE: variable does not get copied if the value at the source and destination are the same.
function varCpy(variableName, source = null, destination = null, updateDestination = false, allowUndefinedVariables = false, bypassValidationCheck) {
    let val = coerce(varGet(variableName, source));
    let val2 = coerce(varGet(variableName, destination));
    if (val === val2 || (val === undefined && !allowUndefinedVariables)) {    
        return false;
    }
    if (val !== undefined) {
        return varSet(variableName, val, destination, updateDestination, bypassValidationCheck);
    }
    return varRem(variableName, destination, updateDestination, bypassValidationCheck);
}


// see varCpy function commment. Additionally allows to specify a different destination variable name with destinationVariableName
function varCpyRen(sourceVariableName, source = null, destinationVariableName, destination = null, updateDestination, allowUndefinedVariables = false, bypassValidationCheck = false) {
    let val = coerce(varGet(sourceVariableName, source));
    let val2 = coerce(varGet(destinationVariableName, destination));
    if (val === val2 || (val === undefined && !allowUndefinedVariables)) {
        return false;
    }
    if (val !== undefined) {
        return varSet(destinationVariableName, val, destination, updateDestination, bypassValidationCheck);
    }
    return varRem(destinationVariableName, destination, updateDestination, bypassValidationCheck);
}

// transfer a variable from source to destination. Specify if you want to update the source and or destination
// allowUndefinedVariables = true will clear the variable at the destination.
function varXfr(variableName, source = null, destination = null, updateSource = false, updateDestination = false, allowUndefinedVariables = false, bypassValidationCheck = false) {
    if (varCpy(variableName, source, destination, updateDestination, allowUndefinedVariables, bypassValidationCheck)) {
        return varRem(variableName, source, updateSource, bypassValidationCheck);
    }
    return false;
}

// transfer a variable from source to destination. Specify if you want to update the source and or destination
// allowUndefinedVariables = true will clear the variable at the destination.
function varXfrRen(sourceVariableName, source = null, destinationVariableName, destination = null, updateSource = false, updateDestination = false, allowUndefinedVariables = false, bypassValidationCheck = false) {
    if (varCpyRen(sourceVariableName, source, destinationVariableName, destination, updateDestination, allowUndefinedVariables, bypassValidationCheck)) {
        return varRem(sourceVariableName, source, updateSource, bypassValidationCheck);
    }
    return false;
}


function varClr(childIframeId = null, update = false, bypassValidationCheck = false) {
    var container = document.getElementById(childIframeId);
    var form;
    if (childIframeId == null) {
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
        updateDisplay(childIframeId, bypassValidationCheck);
    }
}

function updateDisplay(childIframeId = null, bypassValidationCheck = false) {
    var container = document.getElementById(childIframeId);
    var form;
    var btnSubmit;
    if (childIframeId == null) {
        form = document.getElementsByTagName('form')[0];
        btnSubmit = document.getElementById("btnSubmit");
        if (btnSubmit !== null && !bypassValidationCheck) {
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
            else if (btnSubmit !== null && !bypassValidationCheck) {
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

window.addEventListener('scroll', function(event) {
    event.stopPropagation();
    if (varGet("scrollX") != null) {
        //varSet("scrollX", window.scrollX);
        //varSet("scrollY", window.scrollY);
        /*
        if (varSet("scrollX", window.scrollX) || varSet("scrollY", window.scrollY)) {
            alert("changed");
        }
        else {
            alert(window.scrollY);
        }
        */
    }
}, true);

function rememberScrollPosition(childIframeId = null) {
    varSet("scrollX", window.scrollX, childIframeId);
    varSet("scrollY", window.scrollY, childIframeId); 
}

function forgetScrollPosition() {
    varRem("scrollX", childIframeId);
    varRem("ScrollY", childIframeId);
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
        let value = varGet(sortKeyPrefix + keyIndex);

        // end of key list
        if (value === undefined) {
            break;
        }

        // key was removed. All keys to right need to be left-shifted by 1
        if (offsetBy1) {
            varRen(sortKeyPrefix + keyIndex, sortKeyPrefix + (keyIndex - 1));
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
                varSet(sortKeyPrefix + keyIndex, columnName + " DESC");
            }
            // remove the key if it's DESC
            else {
                varRem(sortKeyPrefix + keyIndex);
                // if there are any other keys to the right of this, left shift them by 1
                offsetBy1 = true;
            }
        }
        keyIndex ++;
    }
   
    // if the key wasn't found, append to the end of key list.
    if (!keyFound) {
        varSet(sortKeyPrefix + keyIndex, columnName + " ASC");
    }
    if (refresh) { 
        updateDisplay();
    }
}

function clearSortKeys(tableId) {
    let sortKeyPrefix = tableId + "_SortKey";
    let keyIndex = 1;
    while (true) {
        let value = varGet(sortKeyPrefix + keyIndex);

        // end of key list
        if (value === undefined) {
            break;
        }
        varRem(sortKeyPrefix + keyIndex);
        keyIndex ++;
    }
}

function coerce(data) {
    if (data === undefined || data === null || data.length == 0) {
        return undefined;
    }
    return data;
}