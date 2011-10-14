/**
 * @file
 * Define a generic cache handler
 */
window.Cache = function() {
	var storedValue = {};

	this.set = function(key, value) {
		storedValue[key] = value;
	};
	this.get = function(key) {
		return storedValue[key] ? storedValue[key] : null;
	};
	this.unset = function(key) {
		storedValue[key] = null;
	};
	this.flush = function() {
		storedValue = {};
	};
};
