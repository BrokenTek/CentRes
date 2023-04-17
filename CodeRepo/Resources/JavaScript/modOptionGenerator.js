// existingModString will configure the control (check/select appropriate values)
function generateModOptionDiv(modQuickCode, modName, quantifierString = null, returnHTML = false, radioButton = false) {
    if (modName === undefined || modName === null || modName.length == 0) { return null; }
    let modOptionDivStr;
    let isSel = false;
    let inputType = radioButton ? "radio" : "checkbox";
    if (quantifierString == null || quantifierString == '') {
        // generate a checkbox. No price
        modOptionDivStr = "<input type='" + inputType + "' id='chk" + modQuickCode + "' name='" + modQuickCode + "' value='" + modQuickCode + ",,'>" +
                          "<label class='checkLabel' for='chk" + modQuickCode + "' class='modOption chkModOption'>" + modName + "</label>";
    }
    else if (quantifierString.indexOf(",") == -1 && !isNaN(parseFloat(quantifierString))) {
        // quantifier string should be a price if defined.
        let val = parseFloat(quantifierString); 
        if (val == 0) {
            modOptionDivStr = "<input type='" + inputType + "' id='chk" + modQuickCode + "' name='" + modQuickCode + "' value='" + modQuickCode + ",,'>" +
                 "<label class='checkLabel' for='chk" + modQuickCode + "' class='modOption chkModOption'>FREE - " + modName + "</div></label>";
        }
        else {
            modOptionDivStr = "<input type='" + inputType + "' id='chk" + modQuickCode + "' name='" + modQuickCode + "' value='" + modQuickCode + ",,'>" +
                 "<label class='checkLabel' for='chk" + modQuickCode + "' class='modOption chkModOption'>" + currencyFormatter.format(val) + " - " + modName + "</div></label>";
        }
    }
    else {
        isSel = true;
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
        if (hasBlank) {
            storedStr += "<option value=''></option>"
        }
        modOptionDivStr = storedStr + modOptionDivStr + "</select>";
    }
    if (returnHTML) {
        if (isSel) {
            return "<div class='modOptionDiv selModOptionDiv'>" + modOptionDivStr + "</div>";
        }
        else {
            return "<div class='modOptionDiv chkModOptionDiv'>" + modOptionDivStr + "</div>";
        }
    }
    else {
        let modOptionDiv = document.createElement("div");
        modOptionDiv.classList.add("modOptionDiv");
        if (isSel) {
            modOptionDiv.classList.add("selModOptionDiv");
        }
        else {
            modOptionDiv.classList.add("chkModOptionDiv");
        }
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