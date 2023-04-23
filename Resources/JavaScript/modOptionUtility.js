// existingModString will configure the control (check/select appropriate values)
function generateModOptionDiv(modQuickCode, modName, quantifierString = null, returnHTML = false, categoryType = "OptionalOne", parentQuickCode = null) {
    modName = formatMenuTitle(modName);
    if (!notBlank(modName)) { return null; }
    let modOptionDivStr;
    let divPrefix;
    let multiselect = categoryType.endsWith("Any");
    let inputType = multiselect ? "checkbox" : "radio";
    let allowBlanks = !categoryType.startsWith("Mandatory");
    if (quantifierString == null || quantifierString == '') {
        if (multiselect) {
            // generate a checkbox. No price
            divPrefix = "chk";
            modOptionDivStr = "<input type='" + inputType + "' id='chk" + modQuickCode + "' name='" + modQuickCode + "' value='" + modQuickCode + ",,'>" +
                              "<label class='checkLabel' for='chk" + modQuickCode + "' class='modOption chkModOption'>" + modName + "</label>";
        }
        else {
            // generate a radio. No price
            divPrefix = "rad";
            modOptionDivStr = "<input type='" + inputType + "' id='rad" + modQuickCode + "' name='" + parentQuickCode + "' value='" + modQuickCode + ",,'>" +
                              "<label class='radioLabel' for='rad" + modQuickCode + "' class='modOption radModOption'>" + modName + "</label>";
        }
    }
    else if (quantifierString.indexOf(",") == -1 && !isNaN(parseFloat(quantifierString))) {
        // quantifier string should be a price if defined.
        let val = parseFloat(quantifierString);
        let valStr = (val == 0 ? "FREE" : currencyFormatter.format(val));
        if (multiselect) {
            divPrefix = "chk";
            modOptionDivStr = "<input type='" + inputType + "' id='chk" + modQuickCode + "' name='" + modQuickCode + "' value='" + modQuickCode + ",," + quantifierString + "'>" +
                              "<label class='checkLabel' for='chk" + modQuickCode + "' class='modOption chkModOption'>" + valStr + " - " + modName + "</div></label>";
        }
        else {
            modOptionDivStr = "<input type='" + inputType + "' id='rad" + modQuickCode + "' name='" + parentQuickCode + "' value='" + modQuickCode + ",," + quantifierString + "'>" +
                              "<label class='radioLabel' for='rad" + modQuickCode + "' class='modOption radModOption'>" + valStr + " - " + modName + "</div></label>";
        }
    }
    else if (multiselect) {
        divPrefix = "fst";

        // each value separated by , (COMMA) may contain a pipe, indicating a price is associated with this option.
        modOptionDivStr = "<legend class='modOptionLegend' id = 'ldg" + modQuickCode + "'>" + modName + "</legend>";

        let quantVals = quantifierString.split(",");
        let hasBlank = false;
        let storedStr = modOptionDivStr
        modOptionDivStr = "";
        for (let i = 0; i < quantVals.length; i++) {
            if (quantVals[i] == "") {
                hasBlank = true;
                 continue; 
                }
            if (quantVals[i].indexOf("|") == -1) {
                //<div class='currencyField selCurrencyField empty
                // no price specified. Just text between <select></select>
                modOptionDivStr += "<div class='modOptionDiv selModOptionDiv'>" +
                                    "<input type='" + inputType + "' id='chk" + modQuickCode + "_" + (i + 1) + "' name='" + modQuickCode + "_" + (i + 1) + "' value='" + modQuickCode + "," + quantVals[i] + ",'>" +
                                    "<label class='checkLabel' for='chk" + modQuickCode + "_" + (i + 1) + "' class='modOption chkModOption'>" + quantVals[i] + "</label>" + 
                                    "</div>";
            }
            else {
                // FORMAT: title|price
                let parts = quantVals[i].split("|");
                let orgVal = parts[1];
                if (parts[1] == null || parts[1].length == 0) {
                    parts[1] = "";
                }
                else {
                    let val = parseFloat(parts[1]);
                    if (isNaN(val)) {
                        parts[1] = "⚠";
                    }
                    else if (val == 0) {
                        parts[1] = "FREE";
                    }
                    else {
                        parts[1] = currencyFormatter.format(parts[1]);
                    }
                }
                modOptionDivStr +="<div class='modOptionDiv selModOptionDiv'>" +
                                "<input type='" + inputType + "' id='chk" + modQuickCode + "_" + (i + 1) + "' name='" + modQuickCode + "_" + (i + 1) + "' value='" + modQuickCode + "," + parts[0] + "," + orgVal + "'>" +
                                "<label class='checkLabel' for='chk" + modQuickCode + "_" + (i + 1) + "' class='modOption chkModOption'>" + parts[0] + " - " + parts[1] + "</label>" +
                                "</div>";              
            }
        }
        modOptionDivStr = storedStr + modOptionDivStr;
    }
    else {
       divPrefix = "sel";
        // this will generate a list of options
        
        // each value separated by , (COMMA) may contain a pipe, indicating a price is associated with this option.
        modOptionDivStr = "<label class='selectLabel' for='sel" + modQuickCode + "'>" + modName + "</label>" +
                          "<select id='sel" + modQuickCode + "' class='modOption selModOption'>";

        let quantVals = quantifierString.split(",");
        let hasBlank = false;
        let storedStr = modOptionDivStr;
        modOptionDivStr = "";
        for (let i = 0; i < quantVals.length; i++) {
            if (quantVals[i] == "") {
                hasBlank = true;
                 continue; 
                }
            if (quantVals[i].indexOf("|") == -1) {
                //<div class='currencyField selCurrencyField empty
                // no price specified. Just text between <select></select>
                modOptionDivStr += "<option value='" + modQuickCode + "," + quantVals[i] + ",'>" + quantVals[i] + "</option>";
            }
            else {
                // FORMAT: title|price
                let parts = quantVals[i].split("|");
                let orgVal = parts[1];
                if (parts[1] == null || parts[1].length == 0) {
                    parts[1] = "";
                }
                else {
                    let val = parseFloat(parts[1]);
                    if (isNaN(val)) {
                        parts[1] = "⚠";
                    }
                    else if (val ==0) {
                        parts[1] = "FREE";
                    }
                    else {
                        parts[1] = currencyFormatter.format(parts[1]);
                    }
                }
                modOptionDivStr += "<option value='" + modQuickCode + "," + parts[0] + "," + orgVal + "'>" + parts[1] + " - " + parts[0] + "</option>";
            }
        }
        if (allowBlanks) {
            storedStr += "<option value='/' blankValue></option>"
        }
        modOptionDivStr = storedStr + modOptionDivStr + "</select>";
    }
    if (returnHTML) {
        if (divPrefix == "fst") {
            return "<fieldset class='modOptionFieldset' id='fst" + modQuickCode + "'>" +  modOptionDivStr + "</fieldset>";
        }
        else {
            return "<div class='modOptionDiv " + divPrefix + "ModOptionDiv'>" + modOptionDivStr + "</div>";
        }
    }
    else {
        let modOptionDiv = document.createElement( divPrefix == "fst" ? "fieldset" : "div");
        modOptionDiv.classList.add("modOption" + divPrefix == "fst" ? "Fieldset" : "Div");
        modOptionDiv.innerHTML = modOptionDivStr;
        return modOptionDiv;    
    }
   
}

// https://stackoverflow.com/questions/149055/how-to-format-numbers-as-currency-strings

// Create our number formatter.
const currFormatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  
    // These options are needed to round to whole numbers if that's what you want.
    //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
    //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
  });

const currencyFormatter = currFormatter;

function configureInputs(modNotesString) {
    if (modNotesString == null || modNotesString.length == 0) {
        return;
    }
    let data = modNotesString.split(",");
    let checks = document.querySelectorAll("input[type='checkbox']");
    let radios = document.querySelectorAll("input[type='radio']");
    let options = document.querySelectorAll("option");
    let lastRecordedIndex = -1;
    for (let i = 2; i < data.length; i += 3) {
        for (let j = 0; j < checks.length; j++) {
            if (checks[j].value ==  data[i-2] + "," + data[i-1] + "," + data[i]) {
                checks[j].setAttribute("checked", "");
            } 
        }
        for (let j = 0; j < radios.length; j++) {
            if (radios[j].value ==  data[i-2] + "," + data[i-1] + "," + data[i]) {
                radios[j].setAttribute("checked", "");
            }
        }
        for (let j = 0; j < options.length; j++) {
            if (options[j].value == data[i-2] + "," + data[i-1] + "," + data[i]) {
                options[j].selected = true;
            }
        }
        lastRecordedIndex = i;
    }
    if (lastRecordedIndex < data.length - 1) {
        document.getElementById("txtCustomModNote").value = data[lastRecordedIndex + 1];
    }
}

function generateModString() {
    let modString = "";
    let checks = document.querySelectorAll("input[type='checkbox']");
    let radios = document.querySelectorAll("input[type='radio']");
    let selects = document.querySelectorAll("select");
    for (let j = 0; j < checks.length; j++) {
        if (checks[j].checked) {
            modString += "," + checks[j].value;
        } 
    }
    for (let j = 0; j < radios.length; j++) {
        if (radios[j].checked) {
            modString += "," + radios[j].value;
        } 
    }
    for (let j = 0; j < selects.length; j++) {
        if (selects[j].value.length > 1) {
            modString += "," + selects[j].value;
        }
    }
    with (document.getElementById("txtCustomModNote")) {
        if (value.length > 0) {
            modString += (modString.length == 0 ? "" : ",") + value.replace(",", ".");
        }
    }
    return modString.length > 0 ? modString.substring(1) : "";
}

function calculateModsPrice(modString) {
    if (modString == null || modString.length == 0 || modString.indexOf(",") == -1) {
        return 0;
    }
    let data = modString.split(",");
    let total = 0;
    for (let i = 2; i < data.length; i += 3) {
        let val = parseFloat(data[i]);
        if (!isNaN(val)) {
            total += val;
        }
    }
    return total;
}


function formatMenuTitle(title1 = null, title2 = null) {
    let formattedTitle1 = "";
    let formattedTitle2 = "";
    let titleParts;
    if (notBlank(title1)) {
        if ((' ' + title1).indexOf(' .') == -1) {
            formattedTitle1 = title1;
        }
        else {
            titleParts = title1.split(' ');
            for (let i = 0; i < titleParts.length; i++) {
                if (titleParts[i].indexOf("..") == 0) {
                    formattedTitle1 += " " + titleParts[i].substring(1);
                }
                else if (titleParts[i].indexOf('.') != 0) {
                    formattedTitle1 += " " + titleParts[i];
                }
            }
            if (notBlank(formattedTitle1)) {
                formattedTitle1 = formattedTitle1.substring(1);
            } 
        } 
    }

    if (notBlank(title2)) {
        if ((' ' + title2).indexOf(' .') == -1) {
            formattedTitle2 = title2;
        }
        else {
            titleParts = title2.split(' ');
            for (let i = 0; i < titleParts.length; i++) {
                if (titleParts[i].indexOf("..") == 0) {
                    formattedTitle2 += " " + titleParts[i].substring(1);
                }
                else if (titleParts[i].indexOf('.') != 0) {
                    formattedTitle2 += " " + titleParts[i];
                }
            }
            if (notBlank(formattedTitle2)) {
                formattedTitle2 = formattedTitle2.substring(1);
            }
        }    
    }

    if (notBlank(formattedTitle1) && notBlank(formattedTitle2)) {
        return formattedTitle1 + ": " + formattedTitle2;
    }
    else if (notBlank(formattedTitle1)) {
        return formattedTitle1;
    }
    else if (notBlank(formattedTitle2)) {
        return formattedTitle2;
    }
    else {
        return "";
    }
}

function notBlank(str) {
    return str !== undefined && str !== null && str !== "";
}