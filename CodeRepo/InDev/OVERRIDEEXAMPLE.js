// example on how to override a function in your own file
var overridenTemplateFunction = window.templateFunction;
window.templateFunction = function(arg) {
	/* You code goes here!
	   NOTICE: The original function included an alert
	   that would've popped up.
	*/
}