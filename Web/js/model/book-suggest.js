/**
 * @file
 * Get list of suggested readings for a class
 */
window.BookSuggest = function(regionName) {
	region = $(regionName);

	/**
	 * Get list of books from different vendors
	 *
	 * @param sectionId
	 */
	this.getBookList = function(sectionId) {
		region.addClass('loading');
		$.ajax({
			url: 'college-class-reading',
			type: 'post',
			data: 'section_id=' + sectionId,
			success: function(response) {
				region.removeClass('loading');
				if (response.list) {
					html = '<ul>';
					$.each(response.list, function(index, value) {
						amazonNew = value['amazon']['new']['0'] ? '<span class="buy-new">buy new: ' + value['amazon']['new']['0'] + '</span>' : null;
						html += '<li>' +
							'<img src="' + value['image']['0'] + '" class="cover">' +
							'<span class="title">' + value['title']['0'] + '</span>' +
							amazonNew +
						'</li>';
						console.log(index);
						console.log(value);
					});
					html += '</ul>';

					region.html(html);
				}
			}
		});
	}
}
