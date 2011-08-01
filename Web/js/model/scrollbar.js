/**
 * @file
 * Create JavaScript based vertical scroll bar
 */
$.fn.scrollBar = function() {
	this.wrap('<div class="scrollable" />');
	this.wrap('<div class="scrollable-inner" />');
	/**
	 * @to-do
	 * Implement a javascript based scroll bar
	this.after('<a href="#" class="scroll-bar" />');
	 */
};
