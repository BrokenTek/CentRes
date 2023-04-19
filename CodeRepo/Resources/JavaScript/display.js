window.varExists = function(variableName, childIframeId = null) {
    return varGet(variableName, childIframeId) !== undefined;
}

window.varSet = function(variableName, value, childIframeId = null, update = false, bypassValidationCheck = false) {
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

window.varGet = function(variableName, childIframeId = null) {
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
window.varGetOnce = function(variableName, childIframeId = null, update = false, bypassValidationCheck = false) {
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
window.varRem = function(variableName, childIframeId = null, update = false, bypassValidationCheck = false) {
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

window.varRen = function(oldVarName, newVarName, childIframeId = null, update = false, bypassValidationCheck = false) {
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
window.varCpy = function(variableName, source = null, destination = null, updateDestination = false, allowUndefinedVariables = false, bypassValidationCheck) {
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
window.varCpyRen = function(sourceVariableName, source = null, destinationVariableName, destination = null, updateDestination, allowUndefinedVariables = false, bypassValidationCheck = false) {
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
window.varXfr = function(variableName, source = null, destination = null, updateSource = false, updateDestination = false, allowUndefinedVariables = false, bypassValidationCheck = false) {
    if (varCpy(variableName, source, destination, updateDestination, allowUndefinedVariables, bypassValidationCheck)) {
        return varRem(variableName, source, updateSource, bypassValidationCheck);
    }
    return false;
}

// transfer a variable from source to destination. Specify if you want to update the source and or destination
// allowUndefinedVariables = true will clear the variable at the destination.
window.varXfrRen = function(sourceVariableName, source = null, destinationVariableName, destination = null, updateSource = false, updateDestination = false, allowUndefinedVariables = false, bypassValidationCheck = false) {
    if (varCpyRen(sourceVariableName, source, destinationVariableName, destination, updateDestination, allowUndefinedVariables, bypassValidationCheck)) {
        return varRem(sourceVariableName, source, updateSource, bypassValidationCheck);
    }
    return false;
}


window.varClr = function(childIframeId = null, update = false, bypassValidationCheck = false) {
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

window.updateDisplay = function(childIframeId = null, bypassValidationCheck = false) {
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

window.toggleSortKey = function(tableId, columnName, refresh = true) {
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

window.clearSortKeys = function(tableId) {
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

window.coerce = function(data) {
    if (data === undefined || data === null || data.length == 0) {
        return undefined;
    }
    return data;
}

window.setTitle = function(title, sessionBarTitle) {
    let titleTag = document.getElementsByTagName("title");
    if (titleTag.length == 0) {
        titleTag = document.createElement('title');
        document.querySelector("head").appendChild(titleTag);    
    }
    else {
        titleTag = titleTag[0];
    }
    titleTag.innerText = title;

    let sessionBarTitleDiv = document.querySelector("#sessionBarTitle");
    if (sessionBarTitleDiv != null) {
        sessionBarTitleDiv.innerHTML = sessionBarTitle;
    }
}

////////////////////// PASSING JSON EVENT MESSAGES BETWEEN IFRAMES / PARENT //////////////////////
// JASON Message Format: '{"sessionToken":"SESSION_TOKEN", "eventName":"EVENT_NAME", "eventArguments": {"param1": param1Value, "param2": "param2value"}, iframeId: IFRAMEID }'


//when a json event message object need to be sent to the parent or child iframes
try {
    var cookieName = "804288a34eb7a49b349be68fc6437621cbf25e10d82f4268bb795eca277adedb6a3367add5bfb7cbffb50df150e2e78d26b276f37d32d96cd76746065df58a30cde25c4d9803aa7214dc8f6a985bf8643c341f229b5834964b0f371915d5677e4b579fbab42844cd63ddc3148e4250591277cfc521906bc30cfedd765974c2009ae5fe451ab1890e5ebbfa120ad18934c972618dbe3e";
    var SESSION_TOKEN = document.cookie.match(new RegExp("(^| )" + cookieName + "=([^;]+)"))[2];
}
catch (err) {}

window.dispatchJSONeventCall = function(eventName, eventArgumentsObject, targetIframeIds = []) {
    let message = JSON.stringify({ "sessionToken": SESSION_TOKEN, "eventName": eventName, "eventArguments": eventArgumentsObject, "targetIframeIds": targetIframeIds});
    window.top.postMessage(message, window.location.href);
}


//looks at a message sent form another URL.
//Verifies the sessionToken matches, and calls the appropriate function

//starts at the 
window.addEventListener("message", window.processJSONeventCall);
window.processJSONeventCall = function(messageObject) {
    try {
        let message = JSON.parse(messageObject.data);
       
        if (message.sessionToken === SESSION_TOKEN) {
            if (message.targetIframeIds.length > 0) {
                for (let i = 0; i < message.targetIframeIds.length; i++) {
                    try {
                        document.querySelector("#" + message.targetIframeIds[i]).contentWindow.processJSONeventCall(messageObject);
                    }
                    catch (err) {}
                }
            }
            try {
                document[message.eventName].apply(message.eventArguments);
            }
            catch (err) {}

            let elems = window.document.getElementsByTagName("iframe");
            for (let i = 0; i < elems.length; i++) {
                elems[i].contentWindow.processJSONeventCall(messageObject);
            }
        }
    }
    catch (err) {}
}


