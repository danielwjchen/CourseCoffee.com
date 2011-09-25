/**
 * @file
 * Fetch the converted plain text from server and process the content
 */
window.EditorProcessor = function(processorDataForm) {

	/**
	 * Processor data
	 */
	var _data = $(processorDataForm);

	/**
	 * Clean up line breakers in text
	 */
	var _cleanUpLineBreakers = function(content) {
		return $.trim(content).replace(/\r\n/gi, "\n").replace(/\r/gi, "\n");
	};

	/**
	 * Fetch converted plain text and process the content
	 *
	 * @param callback
	 * 	a callback function to process the content
	 */
	this.processResult = function(callback) {
		$.ajax({
			url: '?q=doc-process',
			type: 'Post',
			cache: false,
			data: _data.serialize(),
			success: function(response){
				if (response.error) {
				}

				response.content = _cleanUpLineBreakers(response.content);
				callback(response);
			}
		});
	};
};
