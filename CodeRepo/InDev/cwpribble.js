// This script only places items into the ticketContainer. It has
//  not functionality other than that. It does however give the
//  elements their necessary attributes, populated as unsubmitted
//  ticketItems. The data that is of type 'hidden' will be used
//  to grab the necessary data using PHP to be sent to the DB.

// Counter to create part of the id for each ticketItem
var ticketItemNumber = 1;

// Move menuItem(s) from the menuContainer to the 
//  ticketContainer with data.


function selectMenuItem(id) {
		
	var menuToTicket = function(id) {
		var menuItem = document.getElementById(id);
        // Get Attributes From Menu Item
        var itemId = menuItem.id;
        var dataText = menuItem.getAttribute('data-text');
        var dataPrice = menuItem.getAttribute('data-price');
        var dataMods = menuItem.getAttribute('data-mods');
		
        // Create ticketItemNumber as a string. This will be the id attr for the ticketItem
		var ticketItemId = "ticketItem" + 
						   document.getElementById('cboSplit').value.toString() + 
						   ticketItemNumber.toString().padStart(3,"0");		
		

    // Create ticketItem span in ticketContainer. This is the root span that incompasses all of
    //   the spans that are displayed for each ticketItem as well as the hidden data spans. All
    //   will have their attributes populated. These are items *NOT SENT TO THE DB YET* (as of now).
        var rootSpan = document.createElement('span');
        rootSpan.setAttribute('class','ticketItem');
        rootSpan.setAttribute('id',ticketItemId);
        rootSpan.appendChild(document.createTextNode(''));
        var ticketContainer = document.getElementById('ticketContainer');
        ticketContainer.appendChild(rootSpan);

    // MOVES TO ADDING THE SPANS THAT DISPLAY DATA TO THE CLIENT.
    //  THESE SPANS ARE VISIBLE TO THE SERVER INTERFACE VIEW.

        // Add the removeTicketItemBtn span
        var xBtn = document.createElement('span');
        xBtn.setAttribute('class','removeTicketItemBtn');  
        xBtn.appendChild(document.createTextNode('X'));
		xBtn.addEventListener('click', window.removeTicketItem);
		
        rootSpan.appendChild(xBtn);

        // Add the ticketItemNumber span
        var tickItemNum = document.createElement('span');
        tickItemNum.setAttribute('class','ticketItemNumber');
        rootSpan.appendChild(tickItemNum);
        
        // Add ticketItemText span
        var tickItemText = document.createElement('span');
        tickItemText.setAttribute('class','ticketItemText');
        tickItemText.appendChild(document.createTextNode(dataText));
        rootSpan.appendChild(tickItemText);
		
        var tickItemPrice = document.createElement('span');
        tickItemPrice.setAttribute('class','ticketItemPrice');
        tickItemPrice.appendChild(document.createTextNode('$'+dataPrice));
        rootSpan.appendChild(tickItemPrice);


		// this code isn't neccessary here, but we can use what you've written when we make modifications
		// to the order, after you hit 'Update' on the mod window and it closes.
		
        /* // Add ticketItemOverrideNote span
        var tickItemOverride = document.createElement('span');
        tickItemOverride.setAttribute('class','ticketItemOverrideNote');
        tickItemOverride.appendChild(document.createTextNode(''));
        rootSpan.appendChild(tickItemOverride);

        // Add ticketItemOverridePrice span
        var tickItemOverridePrice = document.createElement('span');
        tickItemOverridePrice.setAttribute('class','ticketItemOverridePrice');
        tickItemOverridePrice.appendChild(document.createTextNode(''));
        rootSpan.appendChild(tickItemOverridePrice);
        
        // Add modText span
        var modItemText = document.createElement('span');
        modItemText.setAttribute('class','modText');
        modItemText.appendChild(document.createTextNode(''));
        rootSpan.appendChild(modItemText);
        
        // Add modCustom span
        var modItemCustom = document.createElement('span');
        modItemCustom.setAttribute('class','modCustom');
        modItemCustom.appendChild(document.createTextNode(''));
        rootSpan.appendChild(modItemCustom); */

    // MOVES TO ADDING THE INPUT ELEMENTS. THESE ARE ALL HIdDEN. THESE ARE
    //  USED FOR SENDING THE DATA TO THE DATABASE

        // Add hidden ticketItemNumner[] span
        var tickItemNumArray = document.createElement('input');
        tickItemNumArray.setAttribute('type','hidden');
        tickItemNumArray.setAttribute('name','ticketItemNumber[]');
        tickItemNumArray.setAttribute('value',ticketItemId.substring(10));
        rootSpan.appendChild(tickItemNumArray);

        // Add hidden menuItem[] span
        var menuItemArray = document.createElement('input');
        menuItemArray.setAttribute('type','hidden');
        menuItemArray.setAttribute('name','menuItem[]');
        menuItemArray.setAttribute('value',id);
        rootSpan.appendChild(menuItemArray);
        
        // Add hidden customizationNotes[] span
        var customizationNotesArray = document.createElement('input');
        customizationNotesArray.setAttribute('type','hidden');
        customizationNotesArray.setAttribute('name','customizationNotes[]');
        customizationNotesArray.setAttribute('value','');
        rootSpan.appendChild(customizationNotesArray);  

        // Add hidden seat[] span
        var seatNumArray = document.createElement('input');
        seatNumArray.setAttribute('type','hidden');
        seatNumArray.setAttribute('name','seat[]');
        seatNumArray.setAttribute('value',document.getElementById('cboSeat').value.toString());
        rootSpan.appendChild(seatNumArray);
        
        // Add hidden overidePrice[] span
        var overridePriceArray = document.createElement('input');
        overridePriceArray.setAttribute('type','hidden');
        overridePriceArray.setAttribute('name','overidePrice[]');
        overridePriceArray.setAttribute('value','');
        rootSpan.appendChild(overridePriceArray);  

        // Add hidden overideNote[] span
        var overrideNoteArray = document.createElement('input');
        overrideNoteArray.setAttribute('type','hidden');
        overrideNoteArray.setAttribute('name','overideNote[]');
        overrideNoteArray.setAttribute('value','');
        rootSpan.appendChild(overrideNoteArray);  


        // Increment the ticket number suffix. One digit will have 00 before, two
        //  digit will have 0 before, and three digit will have no 0 before. Each
        //  of these options is prefixed with 'ticketItem' and the split number 
        //  (1 is the default).
        ticketItemNumber+=1;

        // Select the ticketItem for further functionality.
        return rootSpan;     
	};
	
	var ticketItem = menuToTicket(id);
	ticketItem.addEventListener('click', window.selectTicketItem);
	document.getElementById('ticketContainer').append(ticketItem);
	window.stateChanged();
}





