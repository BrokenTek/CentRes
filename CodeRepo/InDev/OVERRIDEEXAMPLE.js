
// This example is similar to Python's wrapper class.
// It overrides but still runs the base function.
var overriddenTemplateFunction = window.templateFunction;
window.templateFunction = function(arg) {
	overriddenTemplateFunction(arg);

	/* You code goes here!
	   NOTICE: The original function included an alert
	   that would've popped up.
	*/
}

//This example overrides the old function entirely
window.templateFunction = function(arg) {
	/* You code goes here!
	   NOTICE: The original function included an alert
	   that would've popped up.
	*/
}

