/**
 * @file
 *
 * Extend Autocomplete to provide class reading seach function on item search 
 * page.
 */
ItemSearchSuggest = function(formName, inputName) {

	var _form = $(formName);
	blurInput(formName);

	/**
	 * A callback function to get the item list ob submit
	 */
	var _getItemSuggest = function(section_id) {
		if (section_id != undefined) {
			$('.content').removeClass('hidden');
			var itemList = new ItemSuggest('#item-suggest-list');
			itemList.getItemList(section_id);
		}
		$(document.getElementById('hint-message')).addClass('hidden');
	}

	var _classSuggest = new ClassSuggest(formName, inputName, _getItemSuggest);

	/**
	 * submit the form
	 */
	this.submit = function() {
		var section_id = $('input[name=section_id]', _form).val()
		if (section_id != '') {
			_getItemSuggest(section_id);
		}
	}

};
