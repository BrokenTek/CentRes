
// I know there isn't a need to explicitly define these as variables and
//  that they could be the prefix for the eventListeners at the bottom. This
//  is just for testing at this point. For memory optimization, eventListeners 
//  will not be reference from variables, they will be referenced from DOM 
//  element grabs (document.getElementBy…)

var ticketContainerBtn = document.getElementById('getTicketContainer');
var modsContainerBtn = document.getElementById('getModsContainer');

function viewModsContainer() {
    document.getElementById('ticketContainer').style.display = 'none';
    document.getElementById('modsContainer').style.display = 'initial';
}

function viewTicketContainer() {
    document.getElementById('modsContainer').style.display = 'none';
    document.getElementById('ticketContainer').style.display = 'initial';
}


// EVENT LISTENERS. EVENTUALLY NO FUNCTIONS SHOULD BE WITHIN THIS 
//  DOCUMENT, ONLY EVENT LISTENERS THAT TRIGGER JS FUNCTIONS!
ticketContainerBtn.addEventListener('click', viewTicketContainer);
modsContainerBtn.addEventListener('click', viewModsContainer);

// Andy, this is going to screw up your dropdown for the manditory items when you pull this btw.



