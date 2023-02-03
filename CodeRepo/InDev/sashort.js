// This function checks for any menu items, identified by
// a class name of "menuItem", and adds an event listener
// to check when it is clicked
function createMenuSelectEventhandlers() {
	var elements = document.getElementsByClassName("menuItem");

	var myFunction = function() {
		var attribute = this.id;
		// alert("You clicked menu item " + attribute);
		selectMenuItem( attribute );
	};


	for (var i = 0; i < elements.length; i++) {
	
		elements[i].addEventListener('click', myFunction);
	};
	window.removeEventListener('load', createMenuSelectEventhandlers);
}

window.addEventListener('load', createMenuSelectEventhandlers);










