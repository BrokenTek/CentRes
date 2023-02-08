// This function checks for any menu items, identified by
// a class name of "menuItem", and adds an event listener
// to check when it is clicked
function createMenuSelectEventHandlers() {
	var menuContainer = document.getElementById('menuContainer');
	    var elements = menuContainer.contentWindow.document.getElementsByClassName("menuItem");

	    var myFunction = function() {
			selectMenuItem( this.id );
		};

	for (var i = 0; i < elements.length; i++) {
	    elements[i].addEventListener('pointerdown', myFunction);
	}
};




addEventListener('load', window.createMenuSelectEventHandlers);










