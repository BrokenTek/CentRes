// This function checks for any menu items, identified by
// a class name of "menuItem", and adds an event listener
function createMenuSelectEventhandlers() {
	var elements = document.getElementsByClassName("menuItem");

	var myFunction = function() {
		var attribute = this.id;
		alert("You clicked menu item " + attribute);
		selectMenuItem( id );
	};


	for (var i = 0; i < elements.length; i++) {
	
		elements[i].addEventListener('click', myFunction);
	};
	window.removeEventListener('load', createMenuSelectEventhandlers);
}

window.addEventListener('load', createMenuSelectEventhandlers);





























